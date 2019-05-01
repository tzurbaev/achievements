<?php

namespace Zurbaev\Achievements;

use Zurbaev\Achievements\Contracts\CriteriaHandler;
use Zurbaev\Achievements\Contracts\CriteriaHandlersManager as CriteriaHandlersManagerContract;

class CriteriaHandlersManager implements CriteriaHandlersManagerContract
{
    /** @var array */
    protected $handlers = [];

    /**
     * CriteriaHandlersManager constructor.
     *
     * @param array $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Get the handler for the given criteria type.
     *
     * @param string $type
     *
     * @return CriteriaHandler
     */
    public function getHandlerFor(string $type)
    {
        if (empty($this->handlers[$type])) {
            throw new \InvalidArgumentException('There is no handler for the "'.$type.'" criteria type.');
        } elseif (is_object($this->handlers[$type]) && $this->handlers[$type] instanceof CriteriaHandler) {
            return $this->handlers[$type];
        }

        return new $this->handlers[$type];
    }
}
