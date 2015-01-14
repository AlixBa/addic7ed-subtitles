<?php

namespace AlixBa\Addic7edSubtitles\Helpers;

/**
 * Class IO
 *
 * @package AlixBa\Addic7edSubtitles\Helpers
 */
final class IO
{
    /**
     * @var string
     */
    public $configurationPath;

    /**
     * @var string
     */
    public $configurationReferencePath;

    /**
     * @var string
     */
    public $showsPath;

    /**
     * @var string
     */
    public $languagesPath;

    public function __construct()
    {
        $this->configurationPath          = __DIR__.'/../../../../app/config.json';
        $this->configurationReferencePath = __DIR__.'/../../../../app/config.reference.json';
        $this->showsPath                  = __DIR__.'/../../../../app/shows.json';
        $this->languagesPath              = __DIR__.'/../../../../app/languages.json';
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return json_decode(file_get_contents($this->languagesPath), true);
    }

    /**
     * @return array
     */
    public function getShows()
    {
        return json_decode(file_get_contents($this->showsPath), true);
    }

    /**
     * @param $shows array the shows
     */
    public function saveShows($shows)
    {
        if (file_put_contents($this->showsPath, json_encode($shows, JSON_PRETTY_PRINT)) === false) {
            printf("Something wen wrong while writing [%s].\n", basename($this->showsPath));
        } else {
            printf("Shows saved into [%s].\n", basename($this->showsPath));
        }
    }

    /**
     * @param $directory
     * @param $file
     * @param $content
     */
    public function saveSubtitle($directory, $file, $content)
    {
        if (file_put_contents(sprintf('%s/%s.srt', $directory, $file), $content) === false) {
            printf("Something went wrong while writing [%s/%s.srt].\n", $directory, $file);
        } else {
            printf("Subtitle saved into [%s/%s.srt].\n", $directory, $file);
        }
    }
}