<?php

namespace Ampere\Services\Workshop\Form\Component\Helpers;

/**
 * Trait ErrorPossible
 * @package Ampere\Services\Workshop\Form\Component\Helpers
 */
trait HasPlaceholder
{
    /**
     * @param string $placeholder
     * @return self
     */
    public function placeholder(string $placeholder): self
    {
        $this->params['placeholder'] = $placeholder;
        return $this;
    }
}