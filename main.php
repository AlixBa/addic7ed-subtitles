<?php

if (php_sapi_name() === 'cli') {
    require_once __DIR__.'/vendor/autoload.php';

    $finder = new \AlixBa\Addic7edSubtitles\Jobs\SubtitlesFinder();
    for ($i = 1; $i < count($argv); $i++) {
        $file = pathinfo($argv[$i]);
        printf("Running addic7ed-php for [%s].\n", $file['filename']);

        try {
            $srt = $finder->findSubtitle($file['filename']);
            if (!is_null($srt)) {
                \AlixBa\Addic7edSubtitles\Helpers\Utils::setSubtitle($file['dirname'], $file['filename'], $srt);
            }
            printf("\n");
        } catch (\Exception $e) {
            printf("Error while running addic7ed-php for [%s].\n", $file['filename']);
        }

    }
} else {
    printf("Should be run from CLI.\n");
}
