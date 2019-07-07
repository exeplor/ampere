<?php

namespace Ampere\Services\Workshop\Form;

use Ampere\Services\Workshop\Form\Component\CheckboxComponent;
use Ampere\Services\Workshop\Form\Component\CloseComponent;
use Ampere\Services\Workshop\Form\Component\FormComponent;
use Ampere\Services\Workshop\Form\Component\InputComponent;
use Ampere\Services\Workshop\Form\Component\OpenComponent;
use Ampere\Services\Workshop\Form\Component\RadioComponent;
use Ampere\Services\Workshop\Form\Component\SelectComponent;
use Ampere\Services\Workshop\Form\Component\TextareaComponent;
use Ampere\Services\Workshop\Layout\Builder;

/**
 * Class Form
 * @package Ampere\Services\Workshop\Form
 */
class Form
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var OpenComponent
     */
    private $openComponent;

    /**
     * Form constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return OpenComponent
     */
    public function open(): FormComponent
    {
        $this->openComponent = $this->make(OpenComponent::class);
        return $this->openComponent;
    }

    /**
     * @return CloseComponent
     */
    public function close(): FormComponent
    {
        return $this->make(CloseComponent::class);
    }

    /**
     * @param string $field
     * @param string|null $group
     * @return InputComponent
     */
    public function input(string $field, string $group = null): FormComponent
    {
        return $this->make(InputComponent::class, [
            'name' => $field,
            'group' => $group,
            'error' => $this->getErrorMessage($field),
            'value' => old($field, $this->openComponent->getModelFieldValue($field)),
        ]);
    }

    /**
     * @param string $field
     * @param string|null $group
     * @return TextareaComponent
     */
    public function textarea(string $field, string $group = null): FormComponent
    {
        return $this->make(TextareaComponent::class, [
            'name' => $field,
            'group' => $group,
            'error' => $this->getErrorMessage($field),
            'value' => old($field, $this->openComponent->getModelFieldValue($field)),
        ]);
    }

    /**
     * @param string $field
     * @param string|null $group
     * @return RadioComponent
     */
    public function radio(string $field, string $group = null): FormComponent
    {
        return $this->make(RadioComponent::class, [
            'name' => $field,
            'group' => $group,
            'error' => $this->getErrorMessage($field),
            'value' => old($field, $this->openComponent->getModelFieldValue($field)),
        ]);
    }

    /**
     * @param string $field
     * @param string|null $group
     * @return CheckboxComponent
     */
    public function checkbox(string $field, string $group = null): FormComponent
    {
        return $this->make(CheckboxComponent::class, [
            'name' => $field,
            'group' => $group,
            'value' => old($field, $this->openComponent->getModelFieldValue($field)),
        ]);
    }

    /**
     * @param string $field
     * @param string|null $group
     * @return SelectComponent
     */
    public function select(string $field, string $group = null): FormComponent
    {
        return $this->make(SelectComponent::class, [
            'name' => $field,
            'group' => $group,
            'value' => old($field, $this->openComponent->getModelFieldValue($field)),
        ]);
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function component(string $name, array $params = []): string
    {
        return $this->builder->makeComponent()->build('form.' . $name, $params);
    }

    /**
     * @param string $componentClass
     * @param string|null $group
     * @param array $params
     * @return FormComponent
     */
    private function make(string $componentClass, array $params = []): FormComponent
    {
        /**
         * @var FormComponent $formComponent
         */
        $formComponent = new $componentClass($this, $this->builder->getRequest());
        $formComponent->fill($params);
        return $formComponent;
    }

    /**
     * @param string $field
     * @return null|string
     */
    private function getErrorMessage(string $field): ?string
    {
        $errorBag = session('errors');

        if (empty($errorBag)) {
            return null;
        }

        $errors = $errorBag->get($field);

        return count($errors) > 0 ? $errors[0] : null;
    }
}