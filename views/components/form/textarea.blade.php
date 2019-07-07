<?php
    /**
     * @var string $name Field name
     * @var string $value
     * @var int $rows Default 3
     * @var string $placeholder
     */
?>
<textarea class="form-control" rows="{{ $rows }}" name="{!! $name !!}" {!! isset($placeholder) ? 'placeholder="' . $placeholder . '"' : null !!}>{{ $value }}</textarea>