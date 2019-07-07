<?php

namespace Ampere\Services\Workshop\Form\Component;

use Ampere\Services\Workshop\Form\Component\Helpers\CanDisabled;
use Ampere\Services\Workshop\Form\Component\Helpers\ErrorPossible;
use Ampere\Services\Workshop\Form\Component\Helpers\HasGroup;
use Ampere\Services\Workshop\Form\Component\Helpers\HasPlaceholder;
use Ampere\Services\Workshop\Form\Component\Helpers\HasValue;

/**
 * Class InputComponent
 * @package Ampere\Services\Workshop\Form\Component
 */
class InputComponent extends FormComponent
{
    use ErrorPossible, HasPlaceholder, CanDisabled, HasValue, HasGroup;

    /**
     * @var string
     */
    protected $view = 'input';

    /**
     * @var array
     */
    protected $params = [
        'name' => null,
        'placeholder' => null,
        'error' => null,
        'value' => null,
        'disabled' => null,
        'mask' => null,
        'group' => false,
        'groupInline' => false,
        'type' => 'text'
    ];

    /**
     * @param string $pattern
     * @return InputComponent
     */
    public function mask(string $pattern): self
    {
        $this->params['mask'] = $pattern;
        return $this;
    }

    /**
     * @param string $type
     * @return InputComponent
     */
    public function type(string $type): self
    {
        $this->params['type'] = $type;
        return $this;
    }

    /**
     * @return InputComponent
     */
    public function nullable(): self
    {
        $this->params['value'] = null;
        return $this;
    }
}