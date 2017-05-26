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
     * @var
     */
    private $pattern = '/^(?<showname>.*\w)[\[\. ]+S?(?<season>\d{1,2})[-\. ]?[EX]?(?<episode>\d{2})([-\. ]?[EX]?\d{2})*[\]\. ]+(?<tags>.*)-(?<group>\w*)(\.?\[\w+\])?(\.\w{3})?$/i';

    /**
     * @param $episodeFilename string formatted episode filename
     */
    public function __construct($episodeFilename)
    {
        preg_match($this->pattern, $episodeFilename, $matches);

        $filteredTags = self::filterTags(self::sanitizeTags($matches['tags']));
        $enrichedGroup = self::enrichGroup($matches['group']);

        $this->showName          = $matches['showname'];
        $this->sanitizedShowName = self::sanitizeShowName($matches['showname']);
        $this->season            = $matches['season'];
        $this->ep                = $matches['episode'];
        $this->tags              = array_merge($filteredTags, $enrichedGroup);
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
     * @return string
     */
    public static function sanitizeTags($tags)
    {
        return str_replace('web-dl', 'webdl', strtolower($tags));
    }

    /**
     * @param $tags string identified tags
     *
     * @return array
     */
    public static function filterTags($tags)
    {
        $available = ['proper', 'hdtv', 'x264', '720p', 'webdl', 'webrip'];
        $tags      = preg_split('/\.|_|-|\|/', strtolower($tags));

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
            "immerse",
            "fum"
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
     * @param $tag string tag to look for in tags
     *
     * @return bool true if tag in tags, false otherwise
     */
    public function inTags($tag)
    {
        $inTags = false;
        $tag    = self::sanitizeTags($tag);
        $size   = count($this->tags);

        for ($i = 0; (($i < $size) && !$inTags); $i++) {
            if (strpos($tag, $this->tags[$i]) !== false) {
                $inTags = true;
            }
        }

        return $inTags;
    }

    /**
     * @param $tag string tag to score
     *
     * @return int the score of this tag
     */
    public function score($tag)
    {
        $tags  = preg_split('/\.|_|-|\|/', self::sanitizeTags($tag));

        return count(array_intersect($this->tags, $tags));
    }
}
