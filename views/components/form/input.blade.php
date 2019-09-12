<?php
    /**
     * @var string $name Field name
     * @var string $value
     * @var boolean $disabled
     * @var string $placeholder
     * @var string $type
     * @var string $addon
     * @var boolean $autocomplete
     */
?>
@if($addon)
    <div class="input-group">
@endif

<input class="form-control" type="{{ $type }}" value="{{ $value }}" name="{{ $name }}"{!! isset($placeholder) ? ' placeholder="' . $placeholder . '"' : null !!}{!! $disabled ? ' disabled' : null !!} {!! $autocomplete ? null : ' autocomplete="off"' !!}>

@if($addon)
        <span class="input-group-addon" id="basic-addon2">{{ $addon }}</span>
    </div>
@endif