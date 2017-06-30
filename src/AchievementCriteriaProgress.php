<?php

namespace Zurbaev\Achievements;

class AchievementCriteriaProgress
{
    /**
     * @var int
     */
    public $value = 0;

    /**
     * @var bool
     */
    public $changed = false;

    /**
     * @var bool
     */
    public $completed = false;

    /**
     * AchievementCriteriaProgress constructor.
     *
     * @param int  $value
     * @param bool $changed
     * @param bool $completed
     */
    public function __construct(int $value, bool $changed = false, bool $completed = false)
    {
        $this->value = $value;
        $this->changed = $changed;
        $this->completed = $completed;
    }

    /**
     * Calculates new progress value.
     *
     * @param int    $maxValue
     * @param int    $changeValue
     * @param string $progressType
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    public function getNewValue(int $maxValue, int $changeValue, string $progressType)
    {
        $newValue = 0;

        switch ($progressType) {
            case AchievementCriteriaChange::PROGRESS_SET:
                $newValue = $changeValue;
                break;
            case AchievementCriteriaChange::PROGRESS_ACCUMULATE:
                $newValue = $this->value + $changeValue;

                if ($maxValue > 0) {
                    $newValue = $maxValue - $this->value > $changeValue ? $this->value + $changeValue : $maxValue;
                }
                break;
            case AchievementCriteriaChange::PROGRESS_HIGHEST:
                $newValue = $this->value < $changeValue ? $changeValue : $this->value;
                break;
            default:
                throw new \InvalidArgumentException('Given progress type is invalid ('.$progressType.').');
        }

        return $newValue;
    }
}
