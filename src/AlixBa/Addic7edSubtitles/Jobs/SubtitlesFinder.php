<?php

namespace AlixBa\Addic7edSubtitles\Jobs;

use AlixBa\Addic7edSubtitles\Helpers\ConfigLoader;
use AlixBa\Addic7edSubtitles\Helpers\RequestBuilder;
use AlixBa\Addic7edSubtitles\Helpers\Utils;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SubtitlesFinder
 *
 * @package AlixBa\Addic7edSubtitles\Jobs
 */
final class SubtitlesFinder
{
    /**
     * @var \AlixBa\Addic7edSubtitles\Helpers\ConfigLoader
     */
    private $config;

    /**
     * @var \Goutte\Client
     */
    private $client;

    /**
     * @var \AlixBa\Addic7edSubtitles\Helpers\RequestBuilder
     */
    private $builder;

    /**
     * @var array
     */
    private $shows;

    /**
     * @var array
     */
    private $langs;

    /**
     * @var string
     */
    private $selector = 'div#season > table > tbody > tr.epeven';

    public function __construct()
    {
        $this->config  = new ConfigLoader();
        $this->client  = new Client();
        $this->builder = new RequestBuilder();
        $this->shows   = Utils::getShows();
        $this->langs   = Utils::getLangs();
    }

    /**
     * @param $file string show file name
     *
     * @return null|string
     */
    public function findSubtitle($file)
    {
        $lang = $this->config->getSubtitleLanguage();
        if (isset($this->langs[$lang])) {
            $lang = $this->langs[$lang];

            // TODO improve by a better search method
            $info = Utils::fileToInfo($file);
            if (isset($this->shows[$info['nshow']])) {
                $show = $this->shows[$info['nshow']];
                $url  = $this->builder->getAddictedShowAjaxUrl($show, $info['season'], $lang);

                printf("Trying to get shows from %s.\n", $url);
                $crawler = $this->client->request('GET', $url);
                $lines   = $crawler
                  ->filter($this->selector)
                  ->reduce(function (Crawler $node) use ($info) {
                      $children = $node->children();

                      return
                        $children->getNode(1)->nodeValue == $info['episode']
                        && in_array($children->getNode(4)->nodeValue, $info['groups'])
                        && $children->getNode(5)->nodeValue === 'Completed';
                  });

                if ($lines->count() != 0) {
                    $line     = $lines->first();
                    $download = $line->children()->getNode(9)->firstChild->getAttribute('href');
                    $url      = $this->builder->getSubtitleUrl($download);

                    printf("Downloading subtitle [%s].\n", $url);
                    $headers = $this->builder->getRequestHeaders($show);

                    return $this
                      ->client
                      ->getClient()
                      ->get($url, ['headers' => $headers])
                      ->getBody()
                      ->getContents();

                } else {
                    printf("Missing subtitles for [%s].\n", $file);
                }
            } else {
                printf("Missing show [%s].\n", $info['show']);
            }
        } else {
            printf("Missing language [%s].\n", $lang);
        }

        return null;
    }
}