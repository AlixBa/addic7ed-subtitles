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
    public $sanitizedShowName;

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
        $this->sanitizedShowName = self::sanitizeShowName($matches['showname']);
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
        return strtolower(
            preg_replace('/[^a-zA-Z0-9]+/', '',
                         preg_replace('/[\x00-\x1F\x80-\xFF]+/', 'x', $showName)));
    }

    /**
     * @param $tags string identified tags
     *
     * @return array
     */
    public static function filterTags($tags)
    {
        $available = ['proper', 'hdtv', 'x264', '720p'];
        $tags      = preg_split('/\.|_|-/', strtolower($tags));

        return array_intersect($tags, $available);
    }

    /**
     * @param $group string initial group
     *
     * @return array
     */
    public static function enrichGroup($group)
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

    /**
     * @param $group string group to look for in groups
     *
     * @return bool true if group in groups, false otherwise
     */
    public function inGroups($group)
    {
        $inGroups = false;
        $group    = strtolower($group);
        $size     = count($this->groups);

        for ($i = 0; (($i < $size) && !$inGroups); $i++) {
            if (strpos($group, $this->groups[$i]) !== false) {
                $inGroups = true;
            }
        }

        return $inGroups;
    }
}
