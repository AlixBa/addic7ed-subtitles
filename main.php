<?php

if (php_sapi_name() != 'cli') {
    printf("Should be run from CLI.\n");
    exit(0);
}

if (is_dir(__DIR__ . '/vendor')) {
    // for development
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // for global installation in ~/.composer
    require_once __DIR__ . '/../../autoload.php';
}

array_shift($argv); // take out the first param (script name)

$updater = new \AlixBa\Addic7edSubtitles\Jobs\ShowsUpdater();
$finder  = new \AlixBa\Addic7edSubtitles\Jobs\SubtitlesFinder();
$io      = new \AlixBa\Addic7edSubtitles\Helpers\IO;

$options    = array_filter($argv, function ($arg) { return substr($arg, 0, 2) === '--'; });
$files      = array_diff($argv, $options);
$exceptions = [];

$download = !in_array("--no-download", $options);
$update   = in_array("--update", $options);

if ($update) {
    $updater->updateShows();
}

while (!is_null($filename = array_shift($files))) {
    printf("Running addic7ed-php for [%s].\n", $filename);

    $extensions = ['mp4', 'mkv', 'avi'];
    $file       = pathinfo($filename);
    $basename   = in_array($file['extension'], $extensions) ? $file['filename'] : $basename = $file['basename'];

    try {
        $srt = $finder->findSubtitle($basename, $download);
        if (!is_null($srt)) {
            $io->saveSubtitle($file['dirname'], $basename, $srt);
        }
    } catch (\Exception $e) {
        if (!in_array($filename, $exceptions)) {
            printf("Error while running addic7ed-php for [%s]. Will retry.\n", $basename);
            $exceptions[] = $filename;
            array_push($files, $filename);
        } else {
            printf("Error while running addic7ed-php for [%s].\n", $basename);
        }
    }

    printf("\n");
}
