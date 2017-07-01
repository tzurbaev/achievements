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
     * @var array
     */
    public $data = [];

    /**
     * AchievementCriteriaProgress constructor.
     *
     * @param int  $value
     * @param bool $changed
     * @param bool $completed
     * @param array $data = []
     */
    public function __construct(int $value, bool $changed = false, bool $completed = false, array $data = [])
    {
        $this->value = $value;
        $this->changed = $changed;
        $this->completed = $completed;
        $this->data = $data;
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
