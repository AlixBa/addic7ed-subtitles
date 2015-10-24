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
        $this->assertContains('lol', $episode->groups);
        $this->assertEquals(['hdtv', 'x264'], $episode->tags);
    }

    function testLowerCasedFileName()
    {
        $episode = new Episode('the.big.bang.theory.818.hdtv-lol.mp4');

        $this->assertEquals('the.big.bang.theory', $episode->showName);
        $this->assertEquals('thebigbangtheory', $episode->sanitizedShowName);
        $this->assertEquals(8, $episode->season);
        $this->assertEquals(18, $episode->ep);
        $this->assertContains('lol', $episode->groups);
        $this->assertEquals(['hdtv'], $episode->tags);
    }

    function testUnderscoredFileName()
    {
        $episode = new Episode('Call_The_Midwife.4x08.HDTV_x264-FoV.mp4');

        $this->assertEquals('Call_The_Midwife', $episode->showName);
        $this->assertEquals('callthemidwife', $episode->sanitizedShowName);
        $this->assertEquals(4, $episode->season);
        $this->assertEquals(8, $episode->ep);
        $this->assertContains('fov', $episode->groups);
        $this->assertEquals(['hdtv', 'x264'], $episode->tags);
    }

    function testTagsFiltering()
    {
        $tags = "Hdtv-x264_randomTag.proper";
        $filtered = Episode::filterTags($tags);

        $this->assertContains('hdtv', $filtered);
        $this->assertContains('x264', $filtered);
        $this->assertContains('proper', $filtered);
        $this->assertNotContains('randomtag', $filtered);
    }

    function testDistributionGroupFileName()
    {
        $episode1 = new Episode("Once.Upon.A.Time.S05E02.PROPER.HDTV.x264-KILLERS.[VTV].mp4");
        $episode2 = new Episode("Once.Upon.A.Time.S05E02.PROPER.HDTV.x264-KILLERS.mp4");

        $this->assertEquals($episode1, $episode2);
    }
}
