<?php

namespace Ampere\Services\Workshop\Form\Component\Helpers;

/**
 * Trait ErrorPossible
 * @package Ampere\Services\Workshop\Form\Component\Helpers
 */
trait ErrorPossible
{
    /**
     * @param string $message
     * @return ErrorPossible
     */
    public function error(string $message): self
    {
        $this->params['error'] = $message;
        return $this;
    }
}