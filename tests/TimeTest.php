<?php

namespace Tests;

use bemang\Cache\Time;

class TimeTest extends \PHPUnit\Framework\TestCase
{
    public function testValidInterval()
    {
        $interval = new \DateInterval('P2Y4DT6H8M');
        $this->assertEquals($interval, Time::getValidInterval($interval));
        $this->assertEquals(new \DateInterval('P0Y0DT0H2M'), Time::getValidInterval(2));
    }
}
