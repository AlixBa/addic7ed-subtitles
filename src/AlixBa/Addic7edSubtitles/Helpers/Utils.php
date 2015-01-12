<?php

namespace AlixBa\Addic7edSubtitles\Helpers;

/**
 * Class Utils
 *
 * @package AlixBa\Addic7edSubtitles\Helpers
 */
final class Utils
{
    // PATH from __DIR__
    const SHOWS = '/../../../../app/shows.json';
    const LANGS = '/../../../../app/languages.json';

    /**
     * @param $name string the show name
     *
     * @return string
     */
    public static function sanitizeShowName($name)
    {
        // UTF8 issue with preg_replace
        return mb_ereg_replace('[\W]+', '', $name);
    }

    /**
     * @param $file string filename
     *
     * @return array
     */
    public static function fileToInfo($file)
    {
        $pattern =
          '/^(?<showname>.*\w)[\[\. ]+S?(?<season>\d{1,2})[-\. ]?[EX]?(?<episode>\d{2})'
          .'([-\. ]?[EX]?\d{2})*[\]\. ]+(?<tags>.*)-(?<group>\w*)(\.\w{3})?$/i';

        preg_match($pattern, $file, $matches);

        $tags   = self::filterTags($matches['tags']);
        $groups = self::enrichGroup($matches['group']);

        return [
          'show'    => $matches['showname'],
          'nshow'   => self::sanitizeShowName($matches['showname']),
          'season'  => $matches['season'],
          'episode' => $matches['episode'],
          'tags'    => $tags,
          'groups'  => $groups,
        ];
    }

    /**
     * @param $tags array identified tags
     *
     * @return array
     */
    public static function filterTags($tags)
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
    public static function enrichGroup($group)
    {
        $group  = strtolower($group);
        $groups = [$group];
        $hdtv   = ["lol", "afg", "2hd", "asap", "crimson", "lmao", "bajskorv", "killers", "ftp", "bia", "fov", "dimension", "evolve", "immerse"];
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
     * @return array
     */
    public static function getLangs()
    {
        $file = __DIR__.self::LANGS;

        return json_decode(file_get_contents($file), true);
    }

    /**
     * @return array
     */
    public static function getShows()
    {
        $file = __DIR__.self::SHOWS;

        return json_decode(file_get_contents($file), true);
    }

    /**
     * @param $shows array the shows
     *
     * @return array
     */
    public static function saveShows($shows)
    {
        $file = __DIR__.self::SHOWS;

        file_put_contents($file, json_encode($shows, JSON_PRETTY_PRINT));
        printf("Shows saved into [%s].\n", basename($file));
    }

    /**
     * @param $directory
     * @param $file
     * @param $content
     *
     * @return array
     */
    public static function saveSubtitle($directory, $file, $content)
    {
        file_put_contents(sprintf('%s/%s.srt', $directory, $file), $content);
        printf("Subtitle saved into [%s/%s.srt].\n", $directory, $file);
    }
}