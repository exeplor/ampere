<?php

namespace Ampere\Services\Chart;

use Ampere\Services\Workshop\Component;
use Ampere\Services\Workshop\Layout\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

/**
 * Class Chart
 * @package Ampere\Services\Chart
 */
class Chart
{
    const TYPE_LINE = 'line';
    const TYPE_BAR = 'bar';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @var
     */
    private $name;

    /**
     * @var array
     */
    private $datasets = [];

    /**
     * @var ChartFilter
     */
    public $filter;

    /**
     * @var array
     */
    private $options = [
        'fill' => false,
        'border' => 2,
        'showSum' => false
    ];

    /**
     * @var string
     */
    private $type = self::TYPE_LINE;

    /**
     * @param string $name
     * @param \Closure $callback
     * @return ?ChartMap
     */
    public static function build(string $name, \Closure $callback): ?ChartMap
    {
        $chart = resolve(Chart::class);

        $chart->name = $name;
        $chart->callback = $callback;

        return $chart->getMap();
    }

    /**
     * Chart constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->filter = new ChartFilter($this);
    }

    /**
     * @param string $name
     * @return Chart
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Chart
     */
    public function line(): self
    {
        $this->type = self::TYPE_LINE;
        return $this;
    }

    /**
     * @return Chart
     */
    public function bar(): self
    {
        $this->type = self::TYPE_BAR;
        return $this;
    }

    /**
     * @param array $options
     * @return Chart
     */
    public function options(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * @param string|null $start
     * @param string|null $end
     * @return Chart
     */
    public function addDateRangeFilter(string $start = null, string $end = null): self
    {
        $this->filter->addFilter(ChartFilter::TYPE_DATERANGE, [
            'start' => $start,
            'end' => $end
        ]);

        return $this;
    }

    /**
     * @param string $title
     * @param string $field
     * @param array $options
     * @param string $default
     */
    public function addSelectFilter(string $title, string $field, array $options, string $default = null)
    {
        $this->filter->addFilter(ChartFilter::TYPE_SELECT, [
            'title' => $title,
            'options' => $options,
            'default' => $default
        ], $field);

        return $this;
    }

    /**
     * @param string $label
     * @param array $data
     * @return Chart
     */
    public function add(string $label, array $data): self
    {
        $this->datasets[] = [
            'label' => $label,
            'data' => $data
        ];

        return $this;
    }

    /**
     * @return ChartMap|null
     */
    public function getMap(): ?ChartMap
    {
        if ($this->isAjaxRequest()) {
            if ($this->getRequestFilters() === null) {
                return null;
            }
        }

        ($this->callback)($this);

        $labels = [];
        foreach($this->datasets as $dataset) {
            $labels = array_merge($labels, $dataset['data']);
        }

        $labels = array_keys($labels);
        ksort($labels);

        foreach($this->datasets as $dataset) {
            $set = array_map(function($label) use ($dataset){
                return $dataset['data'][$label] ?? 0;
            }, $labels);

            $datasets[] = [
                'label' => $dataset['label'],
                'data' => $set
            ];
        }

        $chartMap = new ChartMap($this->name, $labels, $datasets, $this->filter->getOriginalFilters(), $this->options, $this->type);

        if ($this->isAjaxRequest()) {
            $component = new Component(new Builder());
            throw new HttpResponseException(response($component->build('chart.chart', ['chart' => $chartMap])));
        }

        return $chartMap;
    }

    /**
     * @return array|null
     */
    public function getRequestFilters(): ?array
    {
        $options = $this->request->input('chart__' . $this->name, null);
        if ($options !== null && !is_iterable($options)) {
            throw new \Exception('Bad filter value');
        }

        return $options;
    }

    /**
     * @return bool
     */
    private function isAjaxRequest(): bool
    {
        return $this->request->ajax();
    }
}