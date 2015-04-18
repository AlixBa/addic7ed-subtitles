<?php

$default = 'Game.of.Thrones.S01E01.HDTV.XviD-FEVER.mp4';
$arg     = isset($_GET['filename']) ? $_GET['filename'] : $default;
$arg     = strpos($arg, '--update') === false ? $arg : $default;

$phpbin   = __DIR__ . '/../bin/addic7ed-php';
$filename = escapeshellarg($arg);
$options  = '--no-download';
$cmd      = sprintf('%s %s %s', $phpbin, $options, $filename);
$output   = shell_exec($cmd);

$input = preg_replace('/^.*addic7ed-php/', 'addic7ed-php', $cmd);

$url    = '_\[((?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?)\]_iuS';
$output = preg_replace('/[\n\r]$/', '', $output);
$output = preg_replace('/[\n\r]/', '<br /><br />', $output);
$output = preg_replace($url, '<a href="$1">$0</a>', $output);

$output = json_encode(['input' => $input, 'output' => $output]);

header('Access-Control-Allow-Origin: http://alixba.github.io');
header('Content-Type: application/json');
header('Cache-Control: max-age=3600');

printf($output);