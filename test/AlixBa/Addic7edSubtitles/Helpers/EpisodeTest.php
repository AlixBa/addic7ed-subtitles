<?php

// XXX assuming input name is: The.Big.Bang.Theory.S08E18.HDTV.x264-LOL.srt, what is show name?!

use AlixBa\Addic7edSubtitles\Helpers\Episode;

class EpisodeTest extends \PHPUnit_Framework_TestCase
{
    function testOne()
    {
        $episode = new Episode('The.Big.Bang.Theory.S08E18.HDTV.x264-LOL.mp4');

        $this->assertEquals('The.Big.Bang.Theory', $episode->showName);
        $this->assertEquals('TheBigBangTheory', $episode->sanitizedShowName);
        $this->assertEquals('08', $episode->season);
        $this->assertEquals('18', $episode->ep);

        $this->assertEquals(['hdtv', 'x264'], $episode->tags);
    }
}
