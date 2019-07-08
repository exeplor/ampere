<?php
    /**
     * @var array $rows
     * @var \Ampere\Services\Grid\GridColumn[] $columns
     */
?>
@foreach($rows as $row)
    <tr{!! isset($row['id']) ? ' data-id="' . $row['id'] . '"' : null !!}>
        @foreach($columns as $field => $column)
            <td{!! ($attribute = $column['attribute']) ? ' data-attribute="' . $attribute . '"' : null  !!}>
                @if(strlen($row[$field]) === 0)
                    <span class="empty-field">(empty)</span>
                @else
                    {!! $row[$field] !!}
                @endif
            </td>
        @endforeach
    </tr>
@endforeach