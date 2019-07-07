<?php

namespace Ampere\Services\Workshop\Form\Component\Helpers;

/**
 * Trait HasGroup
 * @package Ampere\Services\Workshop\Form\Component\Helpers
 */
trait HasGroup
{
    /**
     * @param string $title
     * @return self
     */
    public function group(string $title): self
    {
        $this->params['group'] = $title;
        return $this;
    }

    /**
     * @param bool $inline
     * @return self
     */
    public function inline(bool $inline = true): self
    {
        $this->params['groupInline'] = $inline;
        return $this;
    }

    /**
     * @return self
     */
    public function single(): self
    {
        $this->params['group'] = false;
        $this->params['groupInline'] = false;

        return $this;
    }
}