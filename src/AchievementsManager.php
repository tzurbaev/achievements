<?php

namespace Zurbaev\Achievements;

use Zurbaev\Achievements\Contracts\AchievementsStorageInterface;

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
     * Criteria handlers.
     *
     * @var array
     */
    protected static $handlers = [];

    /**
     * AchievementsManager constructor.
     *
     * @param AchievementsStorageInterface $storage
     */
    public function __construct(AchievementsStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Registers new criteria handler by given type.
     *
     * @param string   $type
     * @param callable $handler
     */
    public static function registerHandler(string $type, callable $handler)
    {
        static::$handlers[$type] = $handler;
    }

    /**
     * Unregisters previously registered criteria handler by given type.
     *
     * @param string $type
     */
    public static function unregisterHandler(string $type)
    {
        unset(static::$handlers[$type]);
    }

    /**
     * Unregisters all previously registered criteria handlers.
     */
    public static function unregisterAllHandlers()
    {
        static::$handlers = [];
    }

    /**
     * Updates criterias progress by given type & checks
     * referred achievements for completeness state.
     *
     * Returns number of updated criterias.
     *
     * @param mixed  $owner
     * @param string $type
     * @param mixed  $data = null
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
            /**
             * @var AchievementCriteria $criteria
             */

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
     * @param mixed               $data = null
     *
     * @return AchievementCriteriaChange|null
     */
    public function getCriteriaChange($owner, AchievementCriteria $criteria, Achievement $achievement, $data = null)
    {
        $handler = static::$handlers[$criteria->type()] ?? null;

        if (!is_callable($handler)) {
            return null;
        }

        $result = call_user_func_array($handler, [
            $owner, $criteria, $achievement, $data,
        ]);

        if ($result instanceof AchievementCriteriaChange) {
            return $result;
        } elseif (is_array($result) && isset($result['value'])) {
            return new AchievementCriteriaChange($result['value'], $result['progress'] ?? $result['progress_type'] ?? null);
        }

        return null;
    }

    /**
     * Updates criteria progress & saves achievement for completeness check (if eligible for).
     *
     * @param mixed               $owner
     * @param AchievementCriteria $criteria
     * @param Achievement         $achievement
     * @param AchievementCriteriaChange $change
     *
     * @return bool
     */
    protected function setCriteriaProgress($owner, AchievementCriteria $criteria, Achievement $achievement, AchievementCriteriaChange $change)
    {
        $maxValue = $criteria->maxValue();
        $changeValue = $change->value;

        if ($maxValue > 0 && $changeValue > $maxValue) {
            $changeValue = $maxValue;
        }

        if (!$criteria->hasProgress()) {
            $newValue = $changeValue;
            $progress = new AchievementCriteriaProgress(0, false);
        } else {
            $progress = $criteria->progress();
            $oldValue = $progress->value;
            $newValue = $progress->getNewValue($maxValue, $changeValue, $change->progressType);

            if ($oldValue === $newValue) {
                return false;
            }
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
        $this->achievementsToCheck[] = $achievement->id();
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

        if (!count($achievements)) {
            $this->achievementsToCheck = [];

            return 0;
        }

        $completedAchievements = [];

        foreach ($achievements as $achievement) {
            /**
             * @var Achievement $achievement
             */

            if ($achievement->completed()) {
                continue;
            }

            if ($this->isCompletedAchievement($achievement)) {
                $completedAchievements[] = $achievement;
            }
        }

        if (count($completedAchievements) > 0) {
            $this->storage->setAchievementsCompleted($owner, $completedAchievements);
        }

        $this->achievementsToCheck = [];

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
        $progress->completed = false;

        if ($progress->value >= $criteria->maxValue()) {
            $progress->completed = true;
        }

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
        $allCompleted = true;
        $count = 0;
        $criteriasCount = count($achievement->criterias());

        foreach ($achievement->criterias() as $criteria) {
            /**
             * @var AchievementCriteria $criteria
             */

            if ($this->isCompletedCriteria($criteria, $criteria->progress())) {
                ++$count;
            } else {
                $allCompleted = false;
                break;
            }

            if ($count >= $criteriasCount) {
                return true;
            }
        }

        return $allCompleted;
    }
}
