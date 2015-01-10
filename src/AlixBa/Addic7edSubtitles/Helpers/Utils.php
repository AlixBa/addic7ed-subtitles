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

        $available = ['hdtv', 'x264', '720p'];
        $tags      = explode('.', strtolower($matches['tags']));
        $tags      = array_intersect($tags, $available);

        $groups = [$matches['group']];
        if (in_array($matches['group'], ['LOL', 'DIMENSION'])) {
            $groups = ['LOL', 'DIMENSION'];
        } elseif (in_array($matches['group'], ['ASAP', 'IMMERSE'])) {
            $groups = ['ASAP', 'IMMERSE'];
        }

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
    public static function setShows($shows)
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
    public static function setSubtitle($directory, $file, $content)
    {
        file_put_contents(sprintf('%s/%s.srt', $directory, $file), $content);
        printf("Subtitle saved into [%s/%s.srt].\n", $directory, $file);
    }
}