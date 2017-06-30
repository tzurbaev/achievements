<?php

namespace Zurbaev\Achievements\Tests;

use PHPUnit\Framework\TestCase;
use Zurbaev\Achievements\AchievementCriteriaChange;
use Zurbaev\Achievements\AchievementCriteriaProgress;

class AchievementCriteriaProgressTest extends TestCase
{
    public function testSetValue()
    {
        $progress = new AchievementCriteriaProgress(10, false, false);
        $expected = 20;
        $actual = $progress->getNewValue(30, 20, AchievementCriteriaChange::PROGRESS_SET);

        $this->assertSame($expected, $actual);
    }

    public function testAccumulateValue()
    {
        $progress = new AchievementCriteriaProgress(10, false, false);
        $expected = 30;
        $actual = $progress->getNewValue(30, 20, AchievementCriteriaChange::PROGRESS_ACCUMULATE);

        $this->assertSame($expected, $actual);
    }

    public function testHighestValue()
    {
        $progress = new AchievementCriteriaProgress(10, false, false);
        $expected = 15;
        $actual = $progress->getNewValue(30, 15, AchievementCriteriaChange::PROGRESS_HIGHEST);

        $this->assertSame($expected, $actual);
    }

    public function testUnknown()
    {
        $progress = new AchievementCriteriaProgress(10, false, false);

        $this->expectException(\InvalidArgumentException::class);
        $progress->getNewValue(30, 15, 'unknow-progress-type');
    }
}
