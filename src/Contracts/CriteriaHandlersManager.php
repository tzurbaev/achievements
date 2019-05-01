<?php

namespace Zurbaev\Achievements\Contracts;

interface CriteriaHandlersManager
{
    /**
     * Get the handler for the given criteria type.
     *
     * @param string $type
     *
     * @return CriteriaHandler
     */
    public function getHandlerFor(string $type);
}
