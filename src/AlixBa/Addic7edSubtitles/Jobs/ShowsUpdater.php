<?php

namespace AlixBa\Addic7edSubtitles\Jobs;

use AlixBa\Addic7edSubtitles\Helpers\RequestBuilder;
use AlixBa\Addic7edSubtitles\Helpers\Utils;
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
     * @var string
     */
    private $href = '/^\/show\/(?<id>\d+)$/';

    /**
     * @var string
     */
    private $selector = 'table.tabel90 > tr > td > h3 > a';

    public function __construct()
    {
        $this->client  = new Client();
        $this->builder = new RequestBuilder();
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
          ->filter($this->selector)
          ->reduce(function (Crawler $node) {
              return preg_match($this->href, $node->attr('href')) === 1;
          })
          ->extract(['_text', 'href']);

        printf("Found [%s] shows.\n", count($_shows));
        $shows = [];
        foreach ($_shows as $show) {
            preg_match($this->href, $show[1], $matches);
            if (!in_array($matches['id'], $this->blacklistedShows())) {
                $name         = Utils::sanitizeShowName($show[0]);
                $shows[$name] = $matches['id'];
            }
        };

        Utils::setShows($shows);
    }

    /**
     * @return array
     */
    public function blacklistedShows()
    {
        return [
          4939
        ];
    }
}