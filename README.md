Please don't use this to overload Addic7ed servers.

## HOW TO

`composer global require alixba/addic7ed-subtitles`<br/>
OR<br/>
`git clone git@github.com:AlixBa/addic7ed-subtitles.git && cd addic7ed-subtitles && composer update`.<br/>

Override wanted parameters from `app/config.reference.json` to `app/config.json`.

## USAGE

`addic7ed-php [--update] [--no-download] [file] [file] [...]`<br/>
`addic7ed-php Marvels.Agent.Carter.S01E01.HDTV.x264-KILLERS.mp4`<br/>
`addic7ed-php --no-download /path/to/Gotham.S01E11.720p.HDTV.X264-DIMENSION.mkv`<br/>
`addic7ed-php --update`<br/>

* --update: updates the shows list
* --no-download: runs addic7ed-php without writing anything on disk

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
