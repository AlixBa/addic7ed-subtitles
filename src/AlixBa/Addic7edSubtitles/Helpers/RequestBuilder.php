<?php

namespace AlixBa\Addic7edSubtitles\Helpers;

/**
 * Class RequestBuilder
 *
 * @package AlixBa\Addic7edSubtitles\Helpers
 */
final class RequestBuilder
{
    /**
     * @var \AlixBa\Addic7edSubtitles\Helpers\ConfigLoader
     */
    private $config;

    public function __construct()
    {
        $this->config = new ConfigLoader();
    }

    /**
     * @return string
     */
    public function getAddictedUrl()
    {
        return sprintf(
          '%s://%s',
          $this->config->getAddictedScheme(),
          $this->config->getAddictedUrl()
        );
    }

    /**
     * @return string
     */
    public function getAddictedShowsUrl()
    {
        return sprintf(
          '%s/%s',
          $this->getAddictedUrl(),
          $this->config->getAddictedShowsUri()
        );
    }

    /**
     * @param $show   int show id
     * @param $season int season number
     * @param $lang   int array of lang
     *
     * @return string
     */
    public function getAddictedShowAjaxUrl($show, $season, $lang)
    {
        $query = $this->config->getAddictedShowAjaxUriQuery();

        $query = str_replace('[SHOW]', $show, $query);
        $query = str_replace('[SEASON]', $season, $query);
        $query = str_replace('[LANGS]', $lang, $query);

        return sprintf(
          '%s/%s?%s',
          $this->getAddictedUrl(),
          $this->config->getAddictedShowAjaxUri(),
          $query
        );
    }

    /**
     * @param $show int show id
     *
     * @return array
     */
    public function getRequestHeaders($show)
    {
        $headers = $this->config->getRequestHeaders();

        if (isset($headers['Host'])) {
            $headers['Host'] = str_replace('[URL]', $this->config->getAddictedUrl(), $headers['Host']);;
        }

        if (isset($headers['Referer'])) {
            $headers['Referer'] = str_replace('[SCHEME]://[URL]', $this->getAddictedUrl(), $headers['Referer']);;
            $headers['Referer'] = str_replace('[SHOW-URI]', $this->config->getAddictedShowUri(), $headers['Referer']);;
            $headers['Referer'] = str_replace('[SHOW]', $show, $headers['Referer']);;
        }

        return $headers;
    }

    /**
     * @param $download string download uri (starting with /)
     *
     * @return string
     */
    public function getSubtitleUrl($download)
    {
        return sprintf(
          '%s%s',
          $this->getAddictedUrl(),
          $download
        );
    }
}