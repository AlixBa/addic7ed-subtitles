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
     * @var \AlixBa\Addic7edSubtitles\Helpers\Config
     */
    private $config;

    public function __construct()
    {
        $this->config = new Config();
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
     * @param $showId string show id
     * @param $season string season number
     * @param $langId string lang id
     *
     * @return string
     */
    public function getAddictedShowAjaxUrl($showId, $season, $langId)
    {
        $query = $this->config->getAddictedShowAjaxUriQuery();

        $query = str_replace('[SHOW]', (int) $showId, $query);
        $query = str_replace('[SEASON]', (int) $season, $query);
        $query = str_replace('[LANGS]', (int) $langId, $query);

        return sprintf(
          '%s/%s?%s',
          $this->getAddictedUrl(),
          $this->config->getAddictedShowAjaxUri(),
          $query
        );
    }

    /**
     * @param $showId string show id
     *
     * @return array
     */
    public function getRequestHeaders($showId)
    {
        $headers = $this->config->getRequestHeaders();

        if (isset($headers['Host'])) {
            $headers['Host'] = str_replace('[URL]', $this->config->getAddictedUrl(), $headers['Host']);;
        }

        if (isset($headers['Referer'])) {
            $headers['Referer'] = str_replace('[SCHEME]://[URL]', $this->getAddictedUrl(), $headers['Referer']);;
            $headers['Referer'] = str_replace('[SHOW-URI]', $this->config->getAddictedShowUri(), $headers['Referer']);;
            $headers['Referer'] = str_replace('[SHOW]', $showId, $headers['Referer']);;
        }

        return $headers;
    }

    /**
     * @param $downloadUri string download uri (starting with /)
     *
     * @return string
     */
    public function getSubtitleUrl($downloadUri)
    {
        return sprintf(
          '%s%s',
          $this->getAddictedUrl(),
          $downloadUri
        );
    }
}