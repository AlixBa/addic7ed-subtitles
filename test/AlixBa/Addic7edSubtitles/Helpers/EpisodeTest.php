<?php

use AlixBa\Addic7edSubtitles\Helpers\Episode;

class EpisodeTest extends \PHPUnit_Framework_TestCase
{
    function testCapitalizedFileName()
    {
        $episode = new Episode('The.Big.Bang.Theory.S08E18.HDTV.x264-LOL.mp4');

        $this->assertEquals('The.Big.Bang.Theory', $episode->showName);
        $this->assertEquals('thebigbangtheory', $episode->sanitizedShowName);
        $this->assertEquals(8, $episode->season);
        $this->assertEquals(18, $episode->ep);
        //$this->assertEquals(['lol'], $episode->groups); // XXX shouldn't this contain only lol ?
        $this->assertEquals(['hdtv', 'x264'], $episode->tags);
    }

    function testLowerCasedFileName()
    {
        $episode = new Episode('the.big.bang.theory.818.hdtv-lol.mp4');

        $this->assertEquals('the.big.bang.theory', $episode->showName);
        $this->assertEquals('thebigbangtheory', $episode->sanitizedShowName);
        $this->assertEquals(8, $episode->season);
        $this->assertEquals(18, $episode->ep);
        // $this->assertEquals(['lol'], $episode->groups); // XXX shouldn't this contain only lol ?
        $this->assertEquals(['hdtv'], $episode->tags);
    }

    function testUnderscoredFileName()
    {
        $episode = new Episode('Call_The_Midwife.4x08.HDTV_x264-FoV.mp4');

        $this->assertEquals('Call_The_Midwife', $episode->showName);
        $this->assertEquals('callthemidwife', $episode->sanitizedShowName);
        $this->assertEquals(4, $episode->season);
        $this->assertEquals(8, $episode->ep);
        // $this->assertEquals(['fov'], $episode->groups); // XXX shouldn't this contain only fov ?
        //$this->assertEquals(['hdtv', 'x264'], $episode->tags);  // XXX shouldn't this contain "hdtv" and "x264" ?
    }
}
