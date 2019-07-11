<?php

    /**
     * @var \Ampere\Services\Grid\Grid $grid
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Component $component
     */

    $columns = $grid->getColumns();
    $rows = $grid->getRows();
    $pagination = $grid->getPagination();

    $tableId = \Str::random(32);
?>
@php($include->css('vendor/datepicker/daterangepicker.css'))
@php($include->js('vendor/moment.js'))
@php($include->js('vendor/datepicker/jquery.daterangepicker.min.js'))

<div class="data-table-body" id="{{ $tableId }}">
    @if($grid->hasExport())
        <a href="#" data-href="{{ url()->current() }}" class="btn btn-success data-table-export-button" type="button">
            <i class="fa fa-cloud-download-alt"></i>
        </a>
    @endif
    <table class="table data-table">
        <thead>
            <tr>
                @foreach($columns as $column)
                <th{!! $column['hasFilter'] ? ' class="data-table-filter"' : null !!}{!! $column['attribute'] ? ' data-attribute="' . $column['attribute'] . '"' : null !!}>

                    @if($column['hasFilter'])
                        <div class="filter-box" data-column="{{ $column['field'] }}">
                            @if($column['isInputFilter'])
                                <input placeholder="{{ $column['title'] }}"{!! $column['date'] ? ' class="data-table-date-filter"' : null !!}>
                            @else
                                <select>
                                    <option value="">{{ $column['title'] }}</option>
                                    @foreach($column['dropdown'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if($column['sortable'])
                                <div class="data-table-sortable">
                                    <i class="fa fa-sort-alpha-down data-table-sort-default"></i>
                                    <i class="fa fa-sort-amount-up data-table-sort-asc"></i>
                                    <i class="fa fa-sort-amount-down data-table-sort-desc"></i>
                                </div>
                            @endif

                            @if($column['searchable'])
                                <i class="fa fa-search data-table-search-icon"></i>
                            @endif
                        </div>
                    @endif

                    <label>{{ $column['title'] }}</label>
                </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php($component->show('grid.rows', [
                'rows' => $rows,
                'columns' => $columns
            ]))
        </tbody>

        <tfoot>
            @php($component->show('grid.pagination', [
                'pagination' => $pagination
            ]))
        </tfoot>
    </table>
</div>

<script>
    dataTable('#{{ $tableId }}').url('{{ url()->current() }}').form('{{ $grid->getTableName() }}');
</script>