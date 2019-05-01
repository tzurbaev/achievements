<?php

namespace Zurbaev\Achievements;

use Zurbaev\Achievements\Contracts\AchievementsStorageInterface;
use Zurbaev\Achievements\Contracts\CriteriaHandler;
use Zurbaev\Achievements\Contracts\CriteriaHandlersManager as CriteriaHandlersManagerContract;

class AchievementsManager
{
    /**
     * @var AchievementsStorageInterface
     */
    protected $storage;

    /**
     * Contains list of achievement IDs that
     * should be checked for completeness
     * in current criterias update.
     *
     * @var array
     */
    protected $achievementsToCheck = [];

    /**
     * Criteria handlers manager.
     *
     * @var CriteriaHandlersManagerContract
     */
    protected $handlers;

    /**
     * AchievementsManager constructor.
     *
     * @param AchievementsStorageInterface    $storage
     * @param CriteriaHandlersManagerContract $handlers
     */
    public function __construct(AchievementsStorageInterface $storage, CriteriaHandlersManagerContract $handlers)
    {
        $this->storage = $storage;
        $this->handlers = $handlers;
    }

    /**
     * Updates criterias progress by given type & checks
     * referred achievements for completeness state.
     *
     * Returns number of updated criterias.
     *
     * @param mixed  $owner
     * @param string $type
     * @param mixed  $data  = null
     *
     * @return int
     */
    public function updateAchievementCriterias($owner, string $type, $data = null): int
    {
        $criterias = $this->storage->getOwnerCriteriasByType($owner, $type, $data);

        if (!count($criterias)) {
            return 0;
        }

        $this->achievementsToCheck = [];

        $achievements = $this->storage->getAchievementsByCriterias($criterias);
        $updatedCriteriasCount = 0;

        foreach ($criterias as $criteria) {
            /** @var AchievementCriteria $criteria*/

            if ($criteria->completed()) {
                continue;
            }

            $achievement = $this->storage->getAchievementForCriteria($criteria, $achievements);

            if (is_null($achievement)) {
                continue;
            }

            $change = $this->getCriteriaChange($owner, $criteria, $achievement, $data);

            if (is_null($change)) {
                continue;
            }

            $this->setCriteriaProgress($owner, $criteria, $achievement, $change);
            $updatedCriteriasCount++;
        }

        if (count($this->achievementsToCheck) > 0) {
            $this->checkCompletedAchievements($owner);
        }

        return $updatedCriteriasCount;
    }

    /**
     * Requests new progress value for given owner & criteria.
     *
     * @param mixed               $owner
     * @param AchievementCriteria $criteria
     * @param Achievement         $achievement
     * @param mixed               $data        = null
     *
     * @return AchievementCriteriaChange|null
     */
    public function getCriteriaChange($owner, AchievementCriteria $criteria, Achievement $achievement, $data = null)
    {
        $handler = $this->handlers->getHandlerFor($criteria->type());

        if (!$handler instanceof CriteriaHandler) {
            return null;
        }

        return $handler->handle($owner, $criteria, $achievement, $data);
    }

    /**
     * Updates criteria progress & saves achievement for completeness check (if eligible for).
     *
     * @param mixed                     $owner
     * @param AchievementCriteria       $criteria
     * @param Achievement               $achievement
     * @param AchievementCriteriaChange $change
     *
     * @return bool
     */
    protected function setCriteriaProgress($owner, AchievementCriteria $criteria, Achievement $achievement, AchievementCriteriaChange $change)
    {
        $maxValue = $criteria->maxValue();
        $changeValue = $change->value;
        $oldValue = null;
        $newValue = $changeValue;
        $progress = new AchievementCriteriaProgress(0, false);

        if ($maxValue > 0 && $changeValue > $maxValue) {
            $changeValue = $maxValue;
        }

        if ($criteria->hasProgress()) {
            $progress = $criteria->progress();
            $oldValue = $progress->value;
            $newValue = $progress->getNewValue($maxValue, $changeValue, $change->progressType);
        }

        if ($oldValue === $newValue) {
            return false;
        }

        $progress->value = $newValue;
        $progress->changed = true;
        $progress->data = $change->progressData;

        $this->storage->setCriteriaProgressUpdated($owner, $criteria, $achievement, $progress);

        if ($this->isCompletedCriteria($criteria, $progress)) {
            $this->completedCriteriaFor($achievement);
        }

        return true;
    }

    /**
     * Saves achievement to completeness check list.
     *
     * @param Achievement $achievement
     */
    protected function completedCriteriaFor(Achievement $achievement)
    {
        if (!in_array($achievement->id(), $this->achievementsToCheck)) {
            $this->achievementsToCheck[] = $achievement->id();
        }
    }

    /**
     * Checks all saved achievements for completeness state.
     *
     * Returns number of completed achievements.
     *
     * @param mixed $owner
     *
     * @return int
     */
    protected function checkCompletedAchievements($owner): int
    {
        $achievements = $this->storage->getAchievementsWithProgressFor($owner, $this->achievementsToCheck);
        $this->achievementsToCheck = [];

        if (!$achievements || !count($achievements)) {
            return 0;
        }

        $completedAchievements = array_filter($achievements, function (Achievement $achievement) {
            return !$achievement->completed() && $this->isCompletedAchievement($achievement);
        });

        if (count($completedAchievements) > 0) {
            $this->storage->setAchievementsCompleted($owner, $completedAchievements);
        }

        return count($completedAchievements);
    }

    /**
     * Determines if given criteria was completed.
     *
     * @param AchievementCriteria         $criteria
     * @param AchievementCriteriaProgress $progress
     *
     * @return bool
     */
    protected function isCompletedCriteria(AchievementCriteria $criteria, AchievementCriteriaProgress $progress): bool
    {
        $progress->completed = $progress->value >= $criteria->maxValue();

        return $progress->completed;
    }

    /**
     * Determines if given achievement was completed.
     *
     * @param Achievement $achievement
     *
     * @return bool
     */
    protected function isCompletedAchievement(Achievement $achievement): bool
    {
        $completedCriterias = array_filter($achievement->criterias(), function (AchievementCriteria $criteria) {
            return $this->isCompletedCriteria($criteria, $criteria->progress());
        });

        return count($achievement->criterias()) === count($completedCriterias);
    }
}
