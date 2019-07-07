<?php

namespace Ampere\Services\Grid\Source;

use Ampere\Services\Grid\GridColumn;
use Ampere\Services\Grid\Source\ExternalQuery\Source;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ExternalDataSource
 * @package Ampere\Services\Grid\Source
 */
class ExternalDataSource extends DataSource
{
    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @var Source
     */
    private $externalQuery;

    /**
     * @param \Closure $callback
     * @return ExternalDataSource
     */
    public function setCallback(\Closure $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return int
     */
    protected function count(): int
    {
        return $this->externalQuery->__totalCount;
    }

    /**
     * @return array
     */
    protected function get(): array
    {
        $filter = collect($this->columns)->map(function(GridColumn $column){
            $value = $this->filter[$column->get('field')] ?? null;

            if ($value) {
                if ($column->get('date')) {
                    list($fromDate, $toDate) = preg_split('/\s*-\s*/', $value);

                    $value = [
                        'from' => $fromDate,
                        'to' => $toDate
                    ];
                }
            }

            return $value;
        })->toArray();

        $query = new Source($filter, $this->limit, $this->offset, $this->sortField, $this->sortDirection);

        $callback = $this->callback;
        $callback($query);

        $this->externalQuery = $query;

        return $this->externalQuery->__data;
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