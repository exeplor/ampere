<?php
    /**
     * @var array $rows
     */
?>
@foreach($rows as $row)
    <tr{!! isset($row['id']) ? ' data-id="' . $row['id'] . '"' : null !!}>
        @foreach($row as $field => $value)
            <td>{!! $value !!}</td>
        @endforeach
    </tr>
@endforeach