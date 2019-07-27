<?php

namespace Ampere\Services\Chart;

use Illuminate\Http\Request;

/**
 * Class ChartFilter
 * @package Ampere\Services\Chart
 */
class ChartFilter
{
    const TYPE_SELECT = 1;
    const TYPE_DATERANGE = 2;

    /**
     * @var Chart
     */
    private $chart;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * ChartFilter constructor.
     * @param Chart $chart
     */
    public function __construct(Chart $chart)
    {
        $this->chart = $chart;
    }

    /**
     * @param int $type
     * @param array $options
     * @param string|null $field
     * @return ChartFilter
     */
    public function addFilter(int $type, array $options, string $field = null): self
    {
        if ($type === self::TYPE_DATERANGE) {
            $field = '__dateRange';
        }

        $this->filters[$field] = [
            'type' => $type,
            'options' => $options
        ];

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        $date = $this->filters['__dateRange']['options']['start'] ?? null;
        if ($filter = $this->getValue('__dateRange')) {
            return explode(' - ', $filter)[0];
        }

        return $date;
    }

    /**
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        $date = $this->filters['__dateRange']['options']['end'] ?? null;
        if ($filter = $this->getValue('__dateRange')) {
            return explode(' - ', $filter)[1];
        }

        return $date;
    }

    /**
     * @param string $filterName
     * @return bool
     */
    public function has(string $filterName): bool
    {
        return !!$this->getValue($filterName);
    }

    /**
     * @param string $filterName
     * @return mixed
     */
    public function getValue(string $filterName)
    {
        if (!in_array($filterName, array_keys($this->filters))) {
            return null;
        }

        $requestFilters = $this->chart->getRequestFilters();
        $requestFilter = $requestFilters[$filterName] ?? null;

        if ($requestFilter) {
            return $requestFilter;
        }

        return $this->filters[$filterName]['options']['default'] ?? null;
    }

    /**
     * @return array
     */
    public function getOriginalFilters(): array
    {
        return $this->filters;
    }
}