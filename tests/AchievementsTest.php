<?php

namespace Zurbaev\Achievements\Tests;

use PHPUnit\Framework\TestCase;
use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;

class AchievementsTest extends TestCase
{
    public function testAttributes()
    {
        $data = [
            'id' => 123,
            'points' => 10,
            'completed' => false,
        ];

        $achievement = new Achievement($data);

        $this->assertSame($data['id'], $achievement->id());
        $this->assertSame($data['points'], $achievement->points());
        $this->assertSame($data['completed'], $achievement->completed());
    }

    public function testAchievementCriterias()
    {
        $achievement = new Achievement();
        $this->assertSame([], $achievement->criterias());
        $this->assertSame([], $achievement->criteriaIds());

        $achievement = new Achievement([
            'criterias' => [
                new AchievementCriteria([
                    'id' => 123,
                    'max_value' => 10,
                ])
            ],
        ]);

        $this->assertSame(1, count($achievement->criterias()));
        $this->assertSame([123], $achievement->criteriaIds());
    }
}
