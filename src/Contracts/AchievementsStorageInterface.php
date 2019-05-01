<?php

namespace Zurbaev\Achievements\Contracts;

use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaProgress;

interface AchievementsStorageInterface
{
    /**
     * Fetches given owner's criterias by given type.
     *
     * @param mixed  $owner
     * @param string $type
     * @param mixed  $data  = null
     *
     * @return array
     */
    public function getOwnerCriteriasByType($owner, string $type, $data = null);

    /**
     * Returns list of criterias' achievements.
     *
     * @param array $criterias
     *
     * @return array
     */
    public function getAchievementsByCriterias(array $criterias);

    /**
     * Extracts single Achievement from given list for given criteria.
     *
     * @param AchievementCriteria $criteria
     * @param array               $achievements
     *
     * @return Achievement
     */
    public function getAchievementForCriteria(AchievementCriteria $criteria, array $achievements);

    /**
     * Loads achievements with progresses for given owner.
     *
     * @param mixed $owner
     * @param array $achievementIds
     *
     * @return array
     */
    public function getAchievementsWithProgressFor($owner, array $achievementIds);

    /**
     * Saves criteria progress for given owner.
     *
     * @param mixed                       $owner
     * @param AchievementCriteria         $criteria
     * @param Achievement                 $achievement
     * @param AchievementCriteriaProgress $progress
     *
     * @return mixed
     */
    public function setCriteriaProgressUpdated($owner, AchievementCriteria $criteria, Achievement $achievement, AchievementCriteriaProgress $progress);

    /**
     * Saves given achievements completeness state for given owner.
     *
     * @param mixed $owner
     * @param array $achievements
     *
     * @return mixed
     */
    public function setAchievementsCompleted($owner, array $achievements);
}
