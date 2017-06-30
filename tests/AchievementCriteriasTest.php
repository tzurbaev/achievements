<?php

namespace Zurbaev\Achievements\Tests;

use PHPUnit\Framework\TestCase;
use Zurbaev\Achievements\AchievementCriteria;

class AchievementCriteriasTest extends TestCase
{
    public function testAttributes()
    {
        $data = [
            'id' => 123,
            'type' => 'blog.write_post',
            'max_value' => 10,
        ];

        $criteria = new AchievementCriteria($data);

        $this->assertSame($data['id'], $criteria->id());
        $this->assertSame($data['type'], $criteria->type());
        $this->assertSame($data['max_value'], $criteria->maxValue());
    }

    public function testRequirements()
    {
        $data = [
            'requirements' => [
                'count' => 5,
                'category' => 1,
            ],
        ];

        $critera = new AchievementCriteria($data);

        $this->assertSame(2, count($critera->requirements()));
        $this->assertTrue($critera->hasRequirement('count'));
        $this->assertTrue($critera->hasRequirement('category'));
        $this->assertFalse($critera->hasRequirement('user'));
        $this->assertSame($data['requirements']['count'], $critera->requirement('count'));
        $this->assertSame($data['requirements']['category'], $critera->requirement('category'));
        $this->assertNull($critera->requirement('user'));
        $this->assertSame('default-user', $critera->requirement('user', 'default-user'));
    }

    public function testProgress()
    {
        $criteria = new AchievementCriteria([]);
        $this->assertFalse($criteria->hasProgress());
        $this->assertFalse($criteria->completed());

        $progress = [
            'value' => 5,
            'completed' => false,
        ];

        $criteria = new AchievementCriteria(['progress' => $progress]);
        $this->assertTrue($criteria->hasProgress());
        $this->assertFalse($criteria->completed());

        $progress = [
            'value' => 5,
            'completed' => true,
        ];

        $criteria = new AchievementCriteria(['progress' => $progress]);
        $this->assertTrue($criteria->hasProgress());
        $this->assertTrue($criteria->completed());

        $progress = $criteria->progress();
        $progress->completed = false;

        $this->assertFalse($criteria->completed());
    }
}
