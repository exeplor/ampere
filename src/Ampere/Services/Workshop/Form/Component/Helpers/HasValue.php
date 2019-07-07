<?php

namespace Ampere\Services\Workshop\Form\Component\Helpers;

/**
 * Trait ErrorPossible
 * @package Ampere\Services\Workshop\Form\Component\Helpers
 */
trait HasValue
{
    /**
     * @param $value
     * @return self
     */
    public function value($value): self
    {
        $this->params['value'] = $value;
        return $this;
    }
}