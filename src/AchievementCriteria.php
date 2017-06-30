<?php

namespace Zurbaev\Achievements;

class AchievementCriteria
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var AchievementCriteriaProgress
     */
    protected $progress;

    /**
     * AchievementCriteria constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Criteria ID.
     *
     * @return mixed|null
     */
    public function id()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Criteria type.
     *
     * @return string|null
     */
    public function type()
    {
        return $this->data['type'] ?? null;
    }

    /**
     * Criteria requirements.
     *
     * @return array
     */
    public function requirements(): array
    {
        if (!isset($this->data['requirements']) || !is_array($this->data['requirements'])) {
            return [];
        }

        return $this->data['requirements'];
    }

    /**
     * Max criteria value.
     *
     * @return int
     */
    public function maxValue(): int
    {
        return intval($this->data['max_value'] ?? 0);
    }

    /**
     * Get single requirement by name.
     *
     * @param string $requirement
     * @param mixed  $default     =  null
     *
     * @return mixed|null
     */
    public function requirement(string $requirement, $default = null)
    {
        return $this->requirements()[$requirement] ?? $default;
    }

    /**
     * Determines if criteria has given requirement.
     *
     * @param string $requirement
     *
     * @return bool
     */
    public function hasRequirement(string $requirement): bool
    {
        return isset($this->requirements()[$requirement]);
    }

    /**
     * Criteria progress.
     *
     * @return AchievementCriteriaProgress
     */
    public function progress(): AchievementCriteriaProgress
    {
        if (!is_null($this->progress)) {
            return $this->progress;
        }

        return $this->progress = new AchievementCriteriaProgress(
            $this->data['progress']['value'] ?? 0,
            $this->data['progress']['changed'] ?? false,
            $this->data['progress']['completed'] ?? false
        );
    }

    /**
     * Determines if criteria has previously stored progress.
     *
     * @return bool
     */
    public function hasProgress(): bool
    {
        return !empty($this->data['progress']);
    }

    /**
     * Determines if criteria was already completed.
     *
     * @return bool
     */
    public function completed(): bool
    {
        return boolval($this->progress()->completed ?? false);
    }
}
