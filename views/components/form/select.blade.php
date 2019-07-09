<?php
    /**
     * @var string $name Field name
     * @var string $value
     * @var array $options
     * @var string $placeholder
     * @var boolean $disabled
     * @var boolean $multiple
     * @var boolean $tags
     * @var string $source
     */

    $id = 'select_' . \Str::random(32);
?>
<select class="form-control" name="{{ $name . ($multiple ? '[]' : null) }}" id="{{ $id }}"{{ $disabled ? ' disabled' :null }}{{$multiple ? ' multiple="multiple"' : null}}>
    @if($source && empty($options) && $value)
        @if(is_array($value))
            @foreach($value as $val)
                <option value="{{$val}}" selected>Loading...</option>
            @endforeach
        @else
            <option value="{{$value}}" selected>Loading...</option>
        @endif
    @endif

    @foreach($options as $key => $title)
        <option value="{{ $key }}"{!! $key == $value ? ' selected' : null !!}>{{ $title }}</option>
    @endforeach
</select>

<script>
    $(function(){
        var object = $('#{{ $id }}');

        var ajaxParams = {
            method: 'POST',
            url: '{{ $source }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    query: params.term,
                    field: '{{ $name }}',
                    _token: '{{ csrf_token() }}'
                }
            }
        };

        var select2Options = {
            {!! $placeholder ? 'placeholder: "' . $placeholder . '",' : null !!}
            {!! $multiple ? 'multiple: true,' : null !!}
            {!! $tags ? 'tags: true,' : null !!}
            {!! $disabled ? 'disabled: true,' : null !!}
            @if($source)
                ajax: ajaxParams,
                templateSelection: function(row) {
                    return $('<span>' + row.text + ' <span class="xid-selection">ID ' + row.id + '</span>' +  '</span>');
                }
            @endif
        }

        object.select2(select2Options);

        @if($source && empty($options) && $value)
            var initialAjax = $.extend({}, ajaxParams, {
                data: {
                    id: {!! is_array($value) ? '[' . implode(',', $value) . ']' : "'$value'" !!},
                    field: '{{ $name }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    object.empty();

                    data.results.map(function(item, id){
                        object.append($('<option>').attr('selected', 'selected').val(item.id).html(item.text));
                        console.log(item, id);
                    });

                    object.select2(select2Options);
                }
            });

            $.ajax(initialAjax);
        @else
            object.select2(select2Options);
        @endif
    });
</script>