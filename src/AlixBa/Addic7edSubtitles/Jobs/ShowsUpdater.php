<?php

namespace AlixBa\Addic7edSubtitles\Jobs;

use AlixBa\Addic7edSubtitles\Helpers\IO;
use AlixBa\Addic7edSubtitles\Helpers\RequestBuilder;
use AlixBa\Addic7edSubtitles\Helpers\Episode;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ShowsUpdater
 *
 * @package AlixBa\Addic7edSubtitles\Jobs
 */
final class ShowsUpdater
{
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
     * @var string
     */
    private $hrefPattern = '/^\/show\/(?<id>\d+)$/';

    public function __construct()
    {
        $this->client  = new Client();
        $this->builder = new RequestBuilder();
        $this->io      = new IO();
    }

    /**
     * Gets all shows and store them in json.
     */
    public function updateShows()
    {
        $url = $this->builder->getAddictedShowsUrl();

        printf("Trying to get shows from [%s].\n", $url);
        $crawler = $this->client->request('GET', $url);
        $_shows  = $crawler
            ->filter('table.tabel90 > tr > td > h3 > a')
            ->reduce(function (Crawler $node) {
                return $this->isShowLink($node->attr('href'));
            })
            ->extract(['_text', 'href']);

        printf("Found [%s] shows.\n", count($_shows));
        $shows = [];
        foreach ($_shows as $show) {
            $id = $this->extractShowId($show[1]);

            // we don't want troublesome ids
            if (!in_array($id, $this->troublesomeShowsId())) {
                $name = Episode::sanitizeShowName($show[0]);

                // if multiple shows name reference the same id
                if (isset($this->troublesomeShowsName()[$name])) {
                    foreach ($this->troublesomeShowsName()[$name] as $name) {
                        $shows[$name] = $id;
                    }
                } else {
                    $shows[$name] = $id;
                }
            }
        };

        $this->io->saveShows($shows);
    }

    /**
     * @return array
     */
    private function troublesomeShowsId()
    {
        return [
            4939 // Sleepy.Hollow, empty show on addic7ed - issue with Sleepy Hollow
        ];
    }

    /**
     * @return array
     */
    private function troublesomeShowsName()
    {
        return [
            'Parenthood' => ['Parenthood', 'Parenthood2010'],
            'TheAmericans2013' => ['TheAmericans2013', 'TheAmericans']
        ];
    }

    /**
     * @param $href string the href to parse
     *
     * @return string
     */
    private function extractShowId($href)
    {
        preg_match($this->hrefPattern, $href, $matches);

        return $matches['id'];
    }

    /**
     * @param $href string the href to test
     *
     * @return bool
     */
    private function isShowLink($href)
    {
        return preg_match($this->hrefPattern, $href) === 1;
    }
}
