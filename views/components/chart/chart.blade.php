<?php
    /**
     * @var \Ampere\Services\Chart\ChartMap $chart
     */

    $id = 'chart_' . \Illuminate\Support\Str::random(32);
    $colors = ['#2196F3', '#FF5722', '#3F51B5', '#009688', '#FFC107'];
    $colors = ['#f44336', '#2196F3', '#009688', '#FF9800', '#9C27B0'];
    $colors = ['#ef5350', '#AB47BC', '#42A5F5', '#26C6DA', '#FFA726'];
    $colors = ['#ef5350', '#42A5F5', '#26A69A', '#FFA726', '#7E57C2'];
    $offset = $chart->options['colorOffset'];
    $colors = array_merge(array_splice($colors, $offset), $colors);
?>

<canvas id="{{ $id }}"></canvas>

<script>
    $(function(){
        var progress = document.getElementById('animationProgress_{{ $id }}');
        var ctx = document.getElementById('{{ $id }}').getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: '{{ $chart->type }}',

            // The data for our dataset
            data: {
                labels: [{!! "'" . implode("', '", $chart->labels) . "'" !!}],
                datasets: [
                        @foreach($chart->datasets as $id => $dataset)
                    {
                        label: '{{ $dataset['label'] }}{!! $chart->options['showSum'] ? ' (' . array_sum($dataset['data']) . ')' : null !!}',
                        backgroundColor: 'rgba({{ implode(', ', sscanf($colors[$id], '#%02x%02x%02x')) }}, {{ $chart->options['transparent'] ? '0.3' : 1 }})',
                        borderColor: '{{ $colors[$id] }}',
                        data: [{!! implode(', ', $dataset['data'])!!}],
                        borderWidth: {{ $chart->options['border'] }},
                        fill: {{ $chart->options['fill'] ? 'true' : 'false' }}
                    },
                    @endforeach
                ]
            },

            // Configuration options go here
            options: {
                legend: {
                    position: 'bottom',
                    labels: {
                        fontColor: 'grey',
                        padding: 20
                    }
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            drawBorder: false,
                            color: ['rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)']
                        }
                    }],

                    xAxes: [{
                        gridLines: {
                            drawBorder: false,
                            color: ['rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)', 'rgba(0, 0, 0, 0.03)']
                        }
                    }]
                },
                tooltips: {
                    position: 'average',
                    mode: 'index',
                    intersect: false,
                }
            }
        });
    });
</script>
