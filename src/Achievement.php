<?php

namespace Zurbaev\Achievements;

class Achievement
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Achievement constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Achievement ID.
     *
     * @return mixed|null
     */
    public function id()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Achievement points.
     *
     * @return int
     */
    public function points(): int
    {
        return intval($this->data['points'] ?? 0);
    }

    /**
     * Achievement criterias.
     *
     * @return array
     */
    public function criterias(): array
    {
        if (!isset($this->data['criterias']) || !is_array($this->data['criterias'])) {
            return [];
        }

        return $this->data['criterias'];
    }

    /**
     * Determines if achievement was already completed.
     *
     * @return bool
     */
    public function completed(): bool
    {
        return boolval($this->data['completed'] ?? false);
    }

    /**
     * Achievement criteria IDs.
     *
     * @return array
     */
    public function criteriaIds(): array
    {
        return array_map(function (AchievementCriteria $criteria) {
            return $criteria->id();
        }, $this->criterias());
    }
}
