<?php

namespace Ampere\Services\Grid\Source;

use Ampere\Services\Grid\GridColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModelDataSource
 * @package Ampere\Services\Grid\Source
 */
class ModelDataSource extends DataSource
{
    /**
     * @var string
     */
    private $model;

    /**
     * @param string $class
     * @return ModelDataSource
     */
    public function setModel(string $class): self
    {
        $this->name = (new $class)->getTable();
        $this->model = $class;
        return $this;
    }

    /**
     * @return int
     */
    protected function count(): int
    {
        return $this->buildQuery()->count();
    }

    /**
     * @return array
     */
    protected function get(): array
    {
        $query = $this->buildQuery()
            ->take($this->limit)
            ->offset($this->offset);

        return $query->getModels();
    }

    /**
     * @return Builder
     */
    private function buildQuery(): Builder
    {
        $modelClass = $this->model;

        /**
         * @var Builder $query
         */
        $query = $modelClass::query();

        foreach($this->columns as $field => $column) {
            $value = $this->filter[$field] ?? null;

            $relations = explode('.', $field);
            if (count($relations) > 1) {
                $joinName = $relations[0];
                $field = $relations[1];

            } else {
                $joinName = false;
            }

            if (strlen($value) > 0) {
                if ($joinName) {

                    /**
                     * @var BelongsTo $foreign
                     */
                    $foreign = $query->getModel()->$joinName();
                    $foreignClass = $foreign->getModel()->getMorphClass();
                    $foreignQuery = $this->getQuery($foreignClass::query(), $column, $field, $value);

                    $ids = $foreignQuery->get()->map(function($model){
                        return $model->id;
                    })->toArray();

                    $query->whereIn($foreign->getForeignKeyName(), $ids);

                } else {
                    $query = $this->getQuery($query, $column, $field, $value);
                }
            }
        }

        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param GridColumn $column
     * @param string $field
     * @param string $value
     * @return Builder
     */
    private function getQuery(Builder $query, GridColumn $column, string $field, string $value): Builder
    {
        if ($column->get('strictSearch')) {
            $query->where($field, $value);

        } else if ($column->get('searchable')) {
            $query->where($field, 'LIKE', '%' . $value . '%');
        }

        if ($column->get('dropdown')) {
            $query->where($field, $value);
        }

        if ($column->get('date')) {
            list($fromDate, $toDate) = preg_split('/\s-\s/', $value);

            $query->where($field, '>=', $fromDate)
                ->where($field, '<=', $toDate);
        }

        return $query;
    }
}