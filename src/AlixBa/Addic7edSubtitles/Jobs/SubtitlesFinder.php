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
     *
     * @return null|string
     */
    public function findSubtitle($episodeFilename)
    {
        $languageId = $this->config->getSubtitleLanguage();
        if (!isset($this->languages[$languageId])) {
            printf("Missing language [%s].\n", $languageId);
            return null;
        }

        $languageId = $this->languages[$languageId];

        $episode = new Episode($episodeFilename);
        if (!isset($this->shows[$episode->showName])) {
            printf("Missing show [%s].\n", $episode->showName);
            return null;
        }

        $showId = $this->shows[$episode->showName];
        $url    = $this->builder->getAddictedShowAjaxUrl($showId, $episode->season, $languageId);

        printf("Trying to get subtitles from %s.\n", $url);
        $crawler           = $this->client->request('GET', $url);
        $matchingSubtitles = $crawler
            ->filter('div#season > table > tbody > tr.epeven')
            ->reduce(function (Crawler $node) use ($episode) {
                $children = $node->children();
                $ep       = $children->getNode(1)->nodeValue;
                $group    = strtolower($children->getNode(4)->nodeValue);
                $status   = strtolower($children->getNode(5)->nodeValue);

                return
                    (int) $ep === (int) $episode->ep
                    && in_array($group, $episode->groups)
                    && $status === 'completed';
          });

        if ($matchingSubtitles->count() == 0) {
            printf("Missing subtitles for [%s].\n", $episodeFilename);
            return null;
        }

        $chosenSubtitle = $matchingSubtitles->first();
        $download       = $chosenSubtitle->children()->getNode(9)->firstChild->getAttribute('href');
        $url            = $this->builder->getSubtitleUrl($download);

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
