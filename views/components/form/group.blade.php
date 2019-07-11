<?php
    /**
     * @var string $label
     * @var string $error Error message
     */
?>

@if($inline)
    <div class="form-inline">
@endif
    <div class="form-group{!! $error ? ' has-error' : null !!}">
        <label class="control-label">{{ $label }}</label>
        {!! $view !!}

        @if($error)
            <span class="help-block">{{ $error }}</span>
        @endif
    </div>
@if($inline)
    </div>
@endif