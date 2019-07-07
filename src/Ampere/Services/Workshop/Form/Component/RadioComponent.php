<?php

namespace Ampere\Services\Workshop\Form\Component;

use Ampere\Services\Workshop\Form\Component\Helpers\ErrorPossible;
use Ampere\Services\Workshop\Form\Component\Helpers\HasGroup;
use Ampere\Services\Workshop\Form\Component\Helpers\HasValue;

/**
 * Class RadioComponent
 * @package Ampere\Services\Workshop\Form\Component
 */
class RadioComponent extends FormComponent
{
    use ErrorPossible, HasValue, HasGroup;

    /**
     * @var string
     */
    protected $view = 'radio';

    /**
     * @var array
     */
    protected $params = [
        'name' => null,
        'items' => [],
        'error' => false,
        'value' => null,
        'group' => false,
        'groupInline' => false
    ];

    /**
     * @param array $items
     * @return RadioComponent
     */
    public function items(array $items): self
    {
        $this->params['items'] = $items;
        return $this;
    }
}