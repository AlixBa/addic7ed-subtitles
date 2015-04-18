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
        $crawler          = $this->client->request('GET', $url);
        $showsLinkAndName = $crawler
            ->filter('table.tabel90 > tr > td > h3 > a')
            ->extract(['_text', 'href']);

        $showsSeasons = $crawler
            ->filter('table.tabel90 > tr > td.newsDate')
            ->extract(['_text']);

        if (count($showsLinkAndName) != count($showsSeasons)) {
            throw new \Exception("Inconsistencies detected while updating shows.");
        }

        printf("Found [%s] shows.\n", count($showsLinkAndName));
        $shows = [];
        foreach ($showsLinkAndName as $n => $show) {
            $id = $this->extractShowId($show[1]);

            if ($this->nonEmptyShow($showsSeasons[$n])) {
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
     * Sometimes Addic7ed/filename differ because of the year in the name.
     * This method maps Addic7ed show name with possible other show names.
     *
     * @return array
     */
    private function mappedShowsNames()
    {
        return [
            'parenthood' => ['parenthood', 'parenthood2010'],
            'theamericans' => ['theamericans', 'theamericans2013'],
            'daredevil' => ['daredevil', 'marvelsdaredevil']
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
     * @param $showSeason string The numbers of seasons and episodes
     *
     * @return bool
     */
    private function nonEmptyShow($showSeason)
    {
        return strpos($showSeason, '0 Seasons') === false;
    }
}
