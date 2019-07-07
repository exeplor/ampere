<?php

namespace Ampere\Services\Workshop\Form\Component;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OpenComponent
 * @package Ampere\Services\Workshop\Form
 */
class OpenComponent extends FormComponent
{
    /**
     * @var string
     */
    protected $view = 'open';

    /**
     * @var array
     */
    protected $params = [
        'method' => 'POST',
        'action' => null,
        'name' => null,
        'model' => null
    ];

    /**
     * @return OpenComponent
     */
    public function get(): self
    {
        $this->params['method'] = 'GET';
        return $this;
    }

    /**
     * @return OpenComponent
     */
    public function post(): self
    {
        $this->params['method'] = 'POST';
        return $this;
    }

    /**
     * @return OpenComponent
     */
    public function put(): self
    {
        $this->params['method'] = 'PUT';
        return $this;
    }

    /**
     * @return OpenComponent
     */
    public function delete(): self
    {
        $this->params['method'] = 'DELETE';
        return $this;
    }

    /**
     * @param Model|null $model
     * @return OpenComponent
     */
    public function model(?Model $model): self
    {
        $this->params['model'] = $model;
        return $this;
    }

    /**
     * @param string $name
     * @return OpenComponent
     */
    public function name(string $name): self
    {
        $this->params['name'] = $name;
        return $this;
    }

    /**
     * @param string $field
     * @return null
     */
    public function getModelFieldValue(string $field)
    {
        return $this->params['model'] ? $this->params['model']->$field : null;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function prepare(array $params = []): array
    {
        if (empty($params['action'])) {
            $params['action'] = $this->request->url();
        }

        return $params;
    }
}