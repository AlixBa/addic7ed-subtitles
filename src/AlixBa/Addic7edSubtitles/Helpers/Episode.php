<?php

namespace AlixBa\Addic7edSubtitles\Helpers;

/**
 * Class Episode
 *
 * @package AlixBa\Addic7edSubtitles\Helpers
 */
final class Episode
{
    /**
     * @var string
     */
    public $showName;

    /**
     * @var string
     */
    public $season;

    /**
     * @var string
     */
    public $ep;

    /**
     * @var array
     */
    public $tags;

    /**
     * @var array
     */
    public $groups;

    /**
     * @var
     */
    private $pattern = '/^(?<showname>.*\w)[\[\. ]+S?(?<season>\d{1,2})[-\. ]?[EX]?(?<episode>\d{2})([-\. ]?[EX]?\d{2})*[\]\. ]+(?<tags>.*)-(?<group>\w*)(\.\w{3})?$/i';

    /**
     * @param $episodeFilename string formatted episode filename
     */
    public function __construct($episodeFilename)
    {
        preg_match($this->pattern, $episodeFilename, $matches);

        $this->showName          = $matches['showname'];
        $this->sanitizedShowname = self::sanitizeShowName($matches['showname']);
        $this->season            = $matches['season'];
        $this->ep                = $matches['episode'];
        $this->tags              = $this->filterTags($matches['tags']);
        $this->groups            = $this->enrichGroup($matches['group']);
    }

    /**
     * @param $showName string the show name
     *
     * @return string
     */
    public static function sanitizeShowName($showName)
    {
        $tmp = str_replace('.', ' ', $showName);

        $showName = str_replace(' ', '.', ucwords($tmp));

        // UTF8 issue with preg_replace
        return mb_ereg_replace('[\W]+', '', $showName);
    }

    /**
     * @param $tags array identified tags
     *
     * @return array
     */
    public function filterTags($tags)
    {
        $available = ['proper', 'hdtv', 'x264', '720p'];
        $tags      = explode('.', strtolower($tags));

        return array_intersect($tags, $available);
    }

    /**
     * @param $group string initial group
     *
     * @return array
     */
    public function enrichGroup($group)
    {
        $group  = strtolower($group);
        $groups = [$group];
        $hdtv   = [
            "lol",
            "afg",
            "2hd",
            "asap",
            "crimson",
            "lmao",
            "bajskorv",
            "killers",
            "ftp",
            "bia",
            "fov",
            "dimension",
            "evolve",
            "immerse"
        ];
        $webdl  = ["ctrlhd", "kings", "eci", "ntb", "btn", "it00nz", "bs", "pod"];
        $dvd    = ["reward", "saints", "sprinter", "demand", "ingot", "haggis"];

        if (in_array($group, $hdtv)) {
            $groups = $hdtv;
        } elseif (in_array($group, $webdl)) {
            $groups = $webdl;
        } elseif (in_array($group, $dvd)) {
            $groups = $dvd;
        }

        return $groups;
    }
}
