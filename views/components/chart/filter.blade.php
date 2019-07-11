<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Component $component
     * @var \Ampere\Services\Chart\ChartMap $chart
     */
?>

@php($include->css('vendor/datepicker/daterangepicker.css'))
@php($include->js('vendor/moment.js'))
@php($include->js('vendor/datepicker/jquery.daterangepicker.min.js'))

<div class="chart-filter-group" data-chart-name="{{ $chart->name }}">
    @foreach($chart->filters as $field => $filter)
        @php($id = \Illuminate\Support\Str::random(16))

        @if($filter['type'] === \Ampere\Services\Chart\ChartFilter::TYPE_DATERANGE)
            <button class="chart-filter-calendar" id="{{ $id }}">
                <i class="fa fa-calendar"></i> <span></span>
            </button>

            <script>
                $(function(){
                    $('#{{ $id }}').daterangepicker({
                        autoApply: true,
                        showCustomRangeLabel: true,
                        alwaysShowCalendars: true,
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        },
                        autoUpdateInput: true,
                        locale: {
                            cancelLabel: 'Clear'
                        },
                        format: 'YYYY-MM-DD',
                        separator : ' - '
                    });

                    $('#{{ $id }}').on('apply.daterangepicker', function(ev, picker) {
                        var date = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');
                        updateChart('{{ $chart->name }}', '{{ $field }}', date);
                        update(date);
                    });

                    function update(date) {
                        $('#{{ $id }}').children('span').html(date);
                    }

                    update('{{ $filter['options']['start'] }} - {{ $filter['options']['end'] }}');
                });
            </script>
        @endif

        @if($filter['type'] === \Ampere\Services\Chart\ChartFilter::TYPE_SELECT)
            <select id="{{ $id }}">
                @foreach($filter['options']['options'] as $value => $title)
                    <option value="{{ $value }}"{{ $filter['options']['default'] === $value ? ' selected' : null }}>{{ $title }}</option>
                @endforeach
            </select>

            <script>
                $('#{{ $id }}').select2();
                $('#{{ $id }}').on('select2:select', function (e) {
                    updateChart('{{ $chart->name }}', '{{ $field }}', e.params.data.id);
                });
            </script>
        @endif
    @endforeach
</div>
