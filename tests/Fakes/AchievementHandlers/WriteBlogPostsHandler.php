<?php

namespace Zurbaev\Achievements\Tests\Fakes\AchievementHandlers;

use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaChange;
use Zurbaev\Achievements\Contracts\CriteriaHandler;

class WriteBlogPostsHandler implements CriteriaHandler
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
        if (!$criteria->hasRequirement('category')) {
            return new AchievementCriteriaChange(1, AchievementCriteriaChange::PROGRESS_ACCUMULATE);
        }

        if (!isset($data['category'])) {
            return null;
        }

        if (intval($criteria->requirement('category')) !== intval($data['category'] ?? 0)) {
            return null;
        }

        return new AchievementCriteriaChange(1, AchievementCriteriaChange::PROGRESS_ACCUMULATE);
    }
}
