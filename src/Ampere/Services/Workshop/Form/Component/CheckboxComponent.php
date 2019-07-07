<?php

namespace Ampere\Services\Workshop\Form\Component;

use Ampere\Services\Workshop\Form\Component\Helpers\ErrorPossible;
use Ampere\Services\Workshop\Form\Component\Helpers\HasGroup;

/**
 * Class CheckboxComponent
 * @package Ampere\Services\Workshop\Form\Component
 */
class CheckboxComponent extends FormComponent
{
    use ErrorPossible, HasGroup;

    /**
     * @var string
     */
    protected $view = 'checkbox';

    /**
     * @var array
     */
    protected $params = [
        'name' => null,
        'error' => false,
        'checked' => false,
        'title' => null,
        'group' => false,
        'groupInline' => false
    ];

    /**
     * @param bool $checked
     * @return CheckboxComponent
     */
    public function checked(bool $checked = true): self
    {
        $this->params['checked'] = !!$checked;
        return $this;
    }

    /**
     * @param string $title
     * @return CheckboxComponent
     */
    public function title(string $title): self
    {
        $this->params['title'] = $title;
        return $this;
    }
}