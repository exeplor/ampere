<?php

namespace Ampere\Services\Chart;

/**
 * Class ChartMap
 *
 * @property-read array $labels
 * @property-read array $datasets
 * @property-read array $filters
 * @property-read array $options
 * @property-read string $name
 * @property-read string $type
 *
 * @package Ampere\Services\Chart
 */
class ChartMap
{
    /**
     * @var array
     */
    private $fields = [
        'labels' => [],
        'datasets' => [],
        'filters' => [],
        'options' => [],
        'name' => null
    ];

    /**
     * ChartMap constructor.
     * @param string $name
     * @param array $labels
     * @param array $datasets
     * @param array $filters
     * @param array $options
     */
    public function __construct(string $name, array $labels, array $datasets, array $filters, array $options, string $type)
    {
        $this->fields['labels'] = $labels;
        $this->fields['datasets'] = $datasets;
        $this->fields['filters'] = $filters;
        $this->fields['options'] = $options;
        $this->fields['name'] = $name;
        $this->fields['type'] = $type;
    }

    /**
     * @param string $key
     */
    public function __get(string $key)
    {
        return $this->fields[$key] ?? null;
    }
}