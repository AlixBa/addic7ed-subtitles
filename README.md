Please don't use this to overload Addic7ed servers.

## HOW TO

Clone this repository and run `composer update`.<br/>
Override wanted parameters from `app/config.reference.json` to `app/config.json`.

## USAGE

`addic7ed-php [file] [file] [...]`<br/>
`addic7ed-php Marvels.Agent.Carter.S01E01.HDTV.x264-KILLERS.mp4 /path/to/Gotham.S01E11.720p.HDTV.X264-DIMENSION.mkv`
`addic7ed-php --update`

## CONTRIBUTE

Feel free to contribute :)

## IDEAS

* Better searching method for show id.
* Allow multiple languages.
* Suffix srt files wih languages ([SHOW].[LANG].srt).
* Make addic7ed-php with options (override config.json on launch).
* Use tag to determine groups?
* Weight default group to find best subtitle if multiple groups match.
* Wait for [this](https://github.com/FriendsOfPHP/Goutte/pull/158) to be merged to set a lower timeout.
* ...