<?php
    /**
     * @var array $rows
     */
?>
@foreach($rows as $row)
    <tr{!! isset($row['id']) ? ' data-id="' . $row['id'] . '"' : null !!}>
        @foreach($row as $field => $value)
            <td>
                @if(strlen($value) === 0)
                    <span class="empty-field">(empty)</span>
                @else
                    {!! $value !!}
                @endif
            </td>
        @endforeach
    </tr>
@endforeach