<?php

namespace Zurbaev\Achievements\Tests\Stubs;

use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaProgress;
use Zurbaev\Achievements\Contracts\AchievementsStorageInterface;

class DummyStorage implements AchievementsStorageInterface
{
    public function getOwnerCriteriasByType($owner, string $type)
    {
        //
    }

    public function getAchievementsByCriterias(array $criterias)
    {
        //
    }

    public function getAchievementForCriteria(AchievementCriteria $criteria, array $achievements)
    {
        //
    }

    public function getAchievementsWithProgressFor($owner, array $achievementIds)
    {
        //
    }

    public function setCriteriaProgressUpdated($owner, AchievementCriteria $criteria, Achievement $achievement, AchievementCriteriaProgress $progress)
    {
        //
    }

    public function setAchievementsCompleted($owner, array $achievements)
    {
        //
    }
}
