<?php

namespace Ampere\Services\Grid\Source\ExternalQuery;

use Ampere\Services\Grid\GridColumn;
use phpDocumentor\Reflection\Types\Iterable_;

/**
 * Class Source
 *
 * @property-read integer $limit
 * @property-read integer $offset
 * @property-read array $filter
 * @property-read string $sort
 * @property-read bool $asc
 * @property-read bool $desc
 *
 * @package Ampere\Services\Grid\Source\ExternalQuery
 */
class Source
{
    /**
     * @var array
     */
    private $params = [];

    /**
     * Source constructor.
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @param null|string $sortField
     * @param null|string $sortDirection
     */
    public function __construct(array $filter, int $limit, int $offset, ?string $sortField, ?string $sortDirection)
    {
        $this->params = [
            'filter' => $filter,
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sortField,
            'asc' => $sortDirection === GridColumn::SORT_ASC,
            'desc' => $sortDirection === GridColumn::SORT_DESC,
            '__totalCount' => 0,
            '__data' => []
        ];
    }

    /**
     * @param int $totalCount
     * @return Source
     */
    public function setCount(int $totalCount): self
    {
        $this->params['__totalCount'] = $totalCount;
        return $this;
    }

    /**
     * @param $data
     * @return Source
     */
    public function setData(array $data): self
    {
        $this->params['__data'] = $data;
        return $this;
    }

    /**
     * @param string $key
     */
    public function __get(string $key)
    {
        if (in_array($key, array_keys($this->params))) {
            return $this->params[$key];
        }
    }
}