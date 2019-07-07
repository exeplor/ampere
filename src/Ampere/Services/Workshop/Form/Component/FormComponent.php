<?php

namespace Ampere\Services\Workshop\Form\Component;

use Ampere\Services\Workshop\Form\Form;
use Illuminate\Http\Request;

/**
 * Class FormComponent
 * @package Ampere\Services\Workshop\Form
 */
abstract class FormComponent
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $params = [

    ];

    /**
     * FormComponent constructor.
     * @param Form $form
     * @param Request $request
     */
    public function __construct(Form $form, Request $request)
    {
        $this->form = $form;
        $this->request = $request;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getParam(string $name)
    {
        return $this->params[$name] ?? null;
    }

    /**
     * @param array $params
     * @return FormComponent
     */
    public function fill(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function prepare(array $params = []): array
    {
        return $params;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $content = $this->form->component($this->view, $this->prepare($this->params));

        if (isset($this->params['group'])) {
            $content = $this->form->component('group', [
                'error' => $this->params['error'] ?? null,
                'label' => $this->params['group'],
                'view' => $content,
                'inline' => $this->params['groupInline'] ?? false
            ]);
        }

        return $content;
    }
}