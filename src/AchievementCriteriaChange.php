<?php

namespace Zurbaev\Achievements;

class AchievementCriteriaChange
{
    const PROGRESS_SET = 'set';
    const PROGRESS_ACCUMULATE = 'accumulate';
    const PROGRESS_HIGHEST = 'highest';

    /**
     * @var int
     */
    public $value = 0;

    /**
     * @var string
     */
    public $progressType = '';

    /**
     * @var array
     */
    public $progressData = [];

    /**
     * AchievementCriteriaChange constructor.
     *
     * @param int         $value
     * @param string|null $progressType
     * @param array $progressData = []
     */
    public function __construct(int $value, string $progressType = null, array $progressData = [])
    {
        $this->value = $value;
        $this->progressType = $progressType ?? static::PROGRESS_HIGHEST;
        $this->progressData = $progressData;
    }
}
