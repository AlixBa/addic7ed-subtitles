<?php

use AlixBa\Addic7edSubtitles\Helpers\Episode;

class EpisodeTest extends \PHPUnit_Framework_TestCase
{
    function testCapitalizedFileName()
    {
        $episode = new Episode('The.Big.Bang.Theory.S08E18.HDTV.x264-LOL.mp4');

        $this->assertEquals('The.Big.Bang.Theory', $episode->showName);
        $this->assertEquals('thebigbangtheory', $episode->sanitizedShowName);
        $this->assertEquals('08', $episode->season);
        $this->assertEquals('18', $episode->ep);

        $this->assertEquals(['hdtv', 'x264'], $episode->tags);
    }

    function testLowerCasedFileName()
    {
        $episode = new Episode('the.big.bang.theory.818.hdtv-lol.mp4');

        $this->assertEquals('the.big.bang.theory', $episode->showName);
        $this->assertEquals('thebigbangtheory', $episode->sanitizedShowName);
        $this->assertEquals('8', $episode->season);
        $this->assertEquals('18', $episode->ep);

        $this->assertEquals(['hdtv'], $episode->tags);
    }
}
