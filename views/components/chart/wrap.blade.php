<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Component $component
     * @var \Ampere\Services\Chart\ChartMap $chart
     * @var string $title
     */
?>

<div class="ibox">
    <div class="ibox-body">
        <div class="chart-box">
            <div class="chart-header">
                <div class="chart-title">
                    {{ $title }}
                </div>
                <div class="chart-filter">
                    @php($component->show('chart.filter', ['chart' => $chart]))
                </div>
            </div>
            <div class="chart-container" data-chart-name="{{ $chart->name }}">
                @php($component->show('chart.chart', ['chart' => $chart]))
            </div>
        </div>
    </div>
</div>
