<?php

namespace Zurbaev\Achievements\Contracts;

use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaChange;

interface CriteriaHandler
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
    public function handle($owner, AchievementCriteria $criteria, Achievement $achievement, $data = null);
}
