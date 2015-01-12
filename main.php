<?php

if (php_sapi_name() === 'cli') {
    require_once __DIR__.'/vendor/autoload.php';

    array_shift($argv); // take out the first param (script name)

    $files = $argv;
    $exceptions = [];
    $finder = new \AlixBa\Addic7edSubtitles\Jobs\SubtitlesFinder();

    while (!is_null($filename = array_shift($files))) {
        $file = pathinfo($filename);
        printf("Running addic7ed-php for [%s].\n", $file['filename']);

        try {
            $srt = $finder->findSubtitle($file['filename']);
            if (!is_null($srt)) {
                \AlixBa\Addic7edSubtitles\Helpers\Utils::saveSubtitle($file['dirname'], $file['filename'], $srt);
            }
        } catch (\Exception $e) {
            if (!in_array($filename, $exceptions)) {
                printf("Error while running addic7ed-php for [%s]. Will retry.\n", $file['filename']);
                $exceptions[] = $filename;
                array_push($files, $filename);
            } else {
                printf("Error while running addic7ed-php for [%s].\n", $file['filename']);
            }
        }

        printf("\n");
    }
} else {
    printf("Should be run from CLI.\n");
}
