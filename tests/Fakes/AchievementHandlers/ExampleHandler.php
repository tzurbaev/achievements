<?php

namespace Zurbaev\Achievements\Tests\Fakes\AchievementHandlers;

use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaChange;
use Zurbaev\Achievements\Contracts\CriteriaHandler;

class ExampleHandler implements CriteriaHandler
{
    /**
     * Handle achievement criteria update.
     *
     * @param mixed               $owner
     * @param AchievementCriteria $criteria
     * @param Achievement         $achievement
     * @param mixed               $data        = null
     *
     * @return AchievementCriteriaChange|null
     */
    public function handle($owner, AchievementCriteria $criteria, Achievement $achievement, $data = null)
    {
        return new AchievementCriteriaChange(10);
    }
}
