<?php

namespace AlixBa\Addic7edSubtitles\Jobs;

use AlixBa\Addic7edSubtitles\Helpers\Config;
use AlixBa\Addic7edSubtitles\Helpers\Episode;
use AlixBa\Addic7edSubtitles\Helpers\IO;
use AlixBa\Addic7edSubtitles\Helpers\RequestBuilder;
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
     * @var \AlixBa\Addic7edSubtitles\Helpers\Config
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
     * @var \AlixBa\Addic7edSubtitles\Helpers\IO
     */
    private $io;

    /**
     * @var array
     */
    private $shows;

    /**
     * @var array
     */
    private $languages;

    public function __construct()
    {
        $this->config    = new Config();
        $this->client    = new Client();
        $this->builder   = new RequestBuilder();
        $this->io        = new IO();
        $this->shows     = $this->io->getShows();
        $this->languages = $this->io->getLanguages();
    }

    /**
     * @param $episodeFilename string show file name
     * @param $download boolean download the file?
     *
     * @return null|string
     */
    public function findSubtitle($episodeFilename, $download)
    {
        $language = $this->config->getSubtitleLanguage();
        if (!isset($this->languages[$language])) {
            printf("Missing language [%s].\n", $language);
            return null;
        }

        $episode = new Episode($episodeFilename);
        if (!isset($this->shows[$episode->sanitizedShowName])) {
            printf("Missing show [%s].\n", $episode->showName);
            return null;
        }

        $languageId = $this->languages[$language];
        $showId     = $this->shows[$episode->sanitizedShowName];
        $url        = $this->builder->getAddictedShowAjaxUrl($showId, $episode->season, $languageId);

        printf("Trying to get subtitles from [%s].\n", $url);
        $crawler           = $this->client->request('GET', $url);
        $matchingSubtitles = $crawler
            ->filter('div#season > table > tbody > tr.epeven')
            ->reduce(function (Crawler $node) use ($episode) {
                $children = $node->children();
                $ep       = $children->getNode(1)->nodeValue;
                $group    = strtolower($children->getNode(4)->nodeValue);
                $status   = strtolower($children->getNode(5)->nodeValue);

                return
                    (int)$ep === (int)$episode->ep
                    && $episode->inTags($group)
                    && strpos($status, '%') === false;
            })
            ->each(function (Crawler $node, $i) use ($episode) {
                $children    = $node->children();
                $group       = strtolower($children->getNode(4)->nodeValue);
                $downloadUri = $children->getNode(9)->firstChild->getAttribute('href');

                $score = $episode->score($group);

                return [ 'uri' => $downloadUri, 'score' => $score ];
            });

        if (count($matchingSubtitles) == 0) {
            printf("Missing subtitles for show [%s] season [%s] episode [%s] \n  and tags [%s].\n",
                   $episode->showName, $episode->season, $episode->ep, implode(', ', $episode->tags));
            return null;
        }

        uasort($matchingSubtitles, function ($a, $b) {
            return $a['score'] < $b['score'];
        });

        $downloadUri = array_shift($matchingSubtitles)['uri'];
        $url         = $this->builder->getSubtitleUrl($downloadUri);

        if ($download === false) {
            printf("Chosen subtitle [%s].\n", $url);
            return null;
        }

        printf("Downloading subtitle [%s].\n", $url);
        $headers = $this->builder->getRequestHeaders($showId);

        return $this
            ->client
            ->getClient()
            ->get($url, ['headers' => $headers])
            ->getBody()
            ->getContents();
    }
}
