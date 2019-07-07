<?php

namespace Ampere\Services\Workshop\Form\Component;

use Ampere\Services\Workshop\Form\Component\Helpers\CanDisabled;
use Ampere\Services\Workshop\Form\Component\Helpers\ErrorPossible;
use Ampere\Services\Workshop\Form\Component\Helpers\HasGroup;
use Ampere\Services\Workshop\Form\Component\Helpers\HasPlaceholder;
use Ampere\Services\Workshop\Form\Component\Helpers\HasValue;

/**
 * Class TextareaComponent
 * @package Ampere\Services\Workshop\Form\Component
 */
class TextareaComponent extends FormComponent
{
    use ErrorPossible, HasPlaceholder, CanDisabled, HasValue, HasGroup;

    /**
     * @var string
     */
    protected $view = 'textarea';

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
        'rows' => 2
    ];

    /**
     * @param int $count
     * @return TextareaComponent
     */
    public function rows(int $count): self
    {
        $this->params['rows'] = $count;
        return $this;
    }
}