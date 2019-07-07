<?php

namespace Ampere\Services\Grid\Source;

use Ampere\Services\Grid\Grid;
use Ampere\Services\Grid\GridColumn;

/**
 * Class ModelDataSource
 * @package Ampere\Services\Grid\Source
 */
abstract class DataSource
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var GridColumn[]
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var string
     */
    protected $sortField;

    /**
     * @var string
     */
    protected $sortDirection;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $totalCount = 0;

    /**
     * @var string
     */
    protected $name = 'default';

    /**
     * @return array
     */
    abstract protected function get(): array;

    /**
     * @return int
     */
    abstract protected function count(): int;

    /**
     * ModelDataSource constructor.
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @param array $columns
     * @return DataSource
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param array $filter
     * @return DataSource
     */
    public function setFilter(array $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param int $limit
     * @return DataSource
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return DataSource
     */
    public function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param null|string $field
     * @return DataSource
     */
    public function setSortField(?string $field): self
    {
        $this->sortField = $field;
        return $this;
    }

    /**
     * @param null|string $direction
     * @return DataSource
     */
    public function setSortDirection(?string $direction): self
    {
        $this->sortDirection = $direction;
        return $this;
    }

    /**
     * Execute query
     */
    public function execute()
    {
        $this->data = $this->get();
        $this->totalCount = $this->count();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}