<?php

namespace Ampere\Services\Workshop\Form\Component;

use Ampere\Services\Workshop\Form\Component\Helpers\CanDisabled;
use Ampere\Services\Workshop\Form\Component\Helpers\ErrorPossible;
use Ampere\Services\Workshop\Form\Component\Helpers\HasGroup;
use Ampere\Services\Workshop\Form\Component\Helpers\HasPlaceholder;
use Ampere\Services\Workshop\Form\Component\Helpers\HasValue;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class InputComponent
 * @package Ampere\Services\Workshop\Form\Component
 */
class SelectComponent extends FormComponent
{
    use ErrorPossible, HasPlaceholder, CanDisabled, HasValue, HasGroup;

    /**
     * @var string
     */
    protected $view = 'select';

    /**
     * @var array
     */
    protected $params = [
        'name' => null,
        'placeholder' => null,
        'error' => false,
        'value' => null,
        'disabled' => false,
        'tags' => false,
        'multiple' => false,
        'options' => [],
        'source' => null,
        'group' => false,
        'groupInline' => false
    ];

    /**
     * @param array $options
     * @return SelectComponent
     */
    public function options(array $options): self
    {
        $this->params['options'] = $options;
        return $this;
    }

    /**
     * @param bool $tags
     * @return SelectComponent
     */
    public function tags(bool $tags = true): self
    {
        $this->params['tags'] = $tags;
        return $this;
    }

    /**
     * @param bool $multiple
     * @return SelectComponent
     */
    public function multiple(bool $multiple = true): self
    {
        $this->params['multiple'] = $multiple;
        return $this;
    }

    /**
     * @param string $source
     * @return SelectComponent
     */
    public function source($source): self
    {
        $this->params['source'] = $source;
        return $this;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function prepare(array $params = []): array
    {
        if (is_iterable($params['value'])) {
            if ($params['value'] instanceof Collection) {
                $params['value'] = $params['value']->map(function($item){
                    return $item->id;
                })->toArray();
            }
        }

        return $params;
    }
}