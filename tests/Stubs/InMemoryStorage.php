<?php

namespace Zurbaev\Achievements\Tests\Stubs;

use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaProgress;
use Zurbaev\Achievements\Contracts\AchievementsStorageInterface;

class InMemoryStorage implements AchievementsStorageInterface
{
    protected $criterias = [];
    protected $achievements = [];
    public $points = [
        'owner' => 0,
    ];

    public function __construct()
    {
        $this->criterias = [
            'write-5-blog-posts' => new AchievementCriteria([
                'id' => 'write-5-blog-posts',
                'type' => 'blog.write_posts',
                'max_value' => 5,
                'requirements' => [
                    'category' => 1,
                    'count' => 5,
                ],
                'progress' => [
                    'completed' => false,
                ],
            ]),
            'write-1-blog-post' => new AchievementCriteria([
                'id' => 'write-1-blog-post',
                'type' => 'blog.write_posts',
                'max_value' => 1,
                'requirements' => [
                    'count' => 1,
                ],
                'progress' => [
                    'completed' => false,
                ],
            ]),
        ];

        $this->achievements = [
            new Achievement([
                'id' => 'ach-write-5-blog-posts',
                'points' => 10,
                'criterias' => [$this->criterias['write-5-blog-posts']],
            ]),
            new Achievement([
                'id' => 'ach-write-1-blog-post',
                'points' => 5,
                'criterias' => [$this->criterias['write-1-blog-post']],
            ]),
        ];
    }

    public function getOwnerCriteriasByType($owner, string $type)
    {
        return array_filter($this->criterias, function (AchievementCriteria $criteria) use ($type) {
            return $criteria->type() === $type;
        });
    }

    public function getAchievementsByCriterias(array $criterias)
    {
        $criteriaIds = array_map(function (AchievementCriteria $criteria) {
            return $criteria->id();
        }, $criterias);

        return array_filter($this->achievements, function (Achievement $ach) use ($criteriaIds) {
            $achievementCriteriaIds = $ach->criteriaIds();

            foreach ($criteriaIds as $crId) {
                if (in_array($crId, $achievementCriteriaIds)) {
                    return true;
                }
            }

            return false;
        });
    }

    public function getAchievementForCriteria(AchievementCriteria $criteria, array $achievements)
    {
        foreach ($achievements as $achievement) {
            if (in_array($criteria->id(), $achievement->criteriaIds())) {
                return $achievement;
            }
        }

        return null;
    }

    public function getAchievementsWithProgressFor($owner, array $achievementIds)
    {
        return array_filter($this->achievements, function (Achievement $achievement) use ($achievementIds) {
            return in_array($achievement->id(), $achievementIds);
        });
    }

    public function getAchievementsByIds(array $achievementIds)
    {
        return array_filter($this->achievements, function (Achievement $achievement) use ($achievementIds) {
            return in_array($achievement->id(), $achievementIds);
        });
    }

    public function setCriteriaProgressUpdated($owner, AchievementCriteria $criteria, Achievement $achievement, AchievementCriteriaProgress $progress)
    {
        //
    }

    public function setAchievementsCompleted($owner, array $achievements)
    {
        foreach ($achievements as $achievement) {
            /**
             * @var Achievement $achievement
             */

            if (!isset($this->points[$owner])) {
                $this->points[$owner] = 0;
            }

            $this->points[$owner] += $achievement->points();
        }
    }
}
