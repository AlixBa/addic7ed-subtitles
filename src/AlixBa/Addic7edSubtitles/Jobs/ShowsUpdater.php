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
            if (!in_array($id, $this->ignoredShowsIds())) {
                $name = Episode::sanitizeShowName($show[0]);

                // if multiple shows name reference the same id
                if (isset($this->mappedShowsNames()[$name])) {
                    foreach ($this->mappedShowsNames()[$name] as $name) {
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
     * Ids ignored by the ShowsUpdater.
     * TODO: Ignore globally if "0 Season, 0 Episode"?
     *
     * @return array
     */
    private function ignoredShowsIds()
    {
        return [
            4939, // Sleepy.Hollow, empty show - issue with Sleepy Hollow - REMOVED from Addic7ed
            5053  // Marvels.Agents.of.S.H.I.E.L.D, empty show - issue with Marvels.Agents.of.S.H.I.E.L.D.
        ];
    }

    /**
     * Sometimes Addic7ed/filename differ because of the year in the name.
     * This method maps Addic7ed show name with possible other show names.
     *
     * @return array
     */
    private function mappedShowsNames()
    {
        return [
            'parenthood' => ['parenthood', 'parenthood2010'],
            'theamericans' => ['theamericans', 'theamericans2013']
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
