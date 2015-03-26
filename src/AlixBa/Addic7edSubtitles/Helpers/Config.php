<?php

namespace AlixBa\Addic7edSubtitles\Helpers;

use Noodlehaus\Config as NConfig;

/**
 * Class Config
 *
 * @package AlixBa\Addic7edSubtitles\Helpers
 */
final class Config extends NConfig
{
    /**
     * Just hardcoded path to config.
     */
    public function __construct()
    {
        $io = new IO();

        if (file_exists($io->configurationPath)) {
            parent::__construct($io->configurationPath);
        } else {
            parent::__construct($io->configurationReferencePath);
        }
    }

    /**
     * @return string
     */
    public function getAddictedScheme()
    {
        return $this->get('addic7ed.scheme', 'http');
    }

    /**
     * @return string
     */
    public function getAddictedUrl()
    {
        return $this->get('addic7ed.url', 'www.addic7ed.com');
    }

    /**
     * @return string
     */
    public function getAddictedShowsUri()
    {
        return $this->get('addic7ed.shows-uri', 'shows.php');
    }

    /**
     * @return string
     */
    public function getAddictedShowUri()
    {
        return $this->get('addic7ed.show-uri', 'shows');
    }

    /**
     * @return string
     */
    public function getAddictedShowAjaxUri()
    {
        return $this->get('addic7ed.shows-ajax-uri', 'ajax_loadShow.php');
    }

    /**
     * @return string
     */
    public function getAddictedShowAjaxUriQuery()
    {
        return $this->get('addic7ed.shows-ajax-uri-query', 'show=[SHOW]&season=[SEASON]&langs=|[LANGS]|&hd=0&hi=0');
    }

    /**
     * @return array
     */
    public function getRequestHeaders()
    {
        $default = [
            'Host'             => '[URL]',
            'Referer'          => '[SCHEME]://[URL]/[SHOW-URI]/[SHOW]',
            'User-Agent'       => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0',
            'Accept'           => 'text/javascript, text/html, application/xml, text/xml, */*',
            'Accept-Language'  => 'en-us,en;q=0.5',
            'Accept-Encoding'  => 'gzip, deflate',
            'X-Requested-With' => 'XMLHttpRequest'
        ];

        return array_merge($default, $this->get('request.headers', []));
    }

    /**
     * @return string
     */
    public function getSubtitleLanguage()
    {
        return $this->get('subtitle.language', 'en');
    }
}
