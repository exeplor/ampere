<?php

namespace Ampere\Services\Grid;

use Illuminate\Http\Request;

/**
 * Class GridColumn
 * @package Ampere\Services\Grid
 */
class GridColumn
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var array
     */
    private $fields = [
        'title' => null,
        'field' => null,
        'searchable' => false,
        'strictSearch' => false,
        'dropdown' => false,
        'sortByDefault' => false,
        'sortDirection' => null,
        'date' => false,
        'display' => null,
        'sortable' => false,
        'export' => true,
        'attribute' => null
    ];

    /**
     * GridColumn constructor.
     * @param Grid $grid
     * @param string $field
     * @param string|null $title
     */
    public function __construct(Grid $grid, string $field, string $title = null)
    {
        $this->grid = $grid;
        $this->fields['field'] = $field;
        $this->fields['title'] = $title;
    }

    /**
     * @return GridColumn
     */
    public function strict(): self
    {
        $this->fields['searchable'] = true;
        $this->fields['strictSearch'] = true;
        return $this;
    }

    /**
     * @return GridColumn
     */
    public function search(): self
    {
        $this->fields['searchable'] = true;
        return $this;
    }

    /**
     * @param array $options
     * @return GridColumn
     */
    public function dropdown(array $options): self
    {
        $this->fields['dropdown'] = $options;
        return $this;
    }

    /**
     * @return GridColumn
     */
    public function date(): self
    {
        $this->fields['date'] = true;
        return $this;
    }

    /**
     * @return GridColumn
     */
    public function sortable(): self
    {
        $this->fields['sortable'] = true;
        return $this;
    }

    /**
     * @return GridColumn
     */
    public function asc(): self
    {
        $this->fields['sortByDefault'] = true;
        $this->fields['sortDirection'] = self::SORT_ASC;
        return $this;
    }

    /**
     * @return GridColumn
     */
    public function desc(): self
    {
        $this->fields['sortByDefault'] = true;
        $this->fields['sortDirection'] = self::SORT_DESC;
        return $this;
    }

    /**
     * @param \Closure $callback
     * @param bool $escape
     * @return GridColumn
     */
    public function display(\Closure $callback, bool $escape = true): self
    {
        $this->fields['display'] = $callback;
        $this->fields['displayEscape'] = $escape;
        return $this;
    }

    /**
     * @param string $field
     * @param string|null $title
     * @return GridColumn
     */
    public function column(string $field, string $title = null): self
    {
        return $this->grid->column($field, $title);
    }

    /**
     * @param string $attribute
     * @return GridColumn
     */
    public function attribute(string $attribute): self
    {
        $this->fields['attribute'] = $attribute;
        return $this;
    }

    /**
     * @param bool $export
     * @return GridColumn
     */
    public function export(bool $export): self
    {
        $this->fields['export'] = $export;
        return $this;
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function get(string $field = null)
    {
        return $field ? ($this->fields[$field] ?? null) : $this->fields;
    }
}