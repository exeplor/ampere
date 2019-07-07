<?php

namespace Ampere\Services\Workshop\Form\Component\Helpers;

/**
 * Trait ErrorPossible
 * @package Ampere\Services\Workshop\Form\Component\Helpers
 */
trait CanDisabled
{
    /**
     * @param bool $disabled
     * @return self
     */
    public function disabled(bool $disabled = true): self
    {
        $this->params['disabled'] = $disabled;
        return $this;
    }
}