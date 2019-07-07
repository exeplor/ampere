<?php
    /**
     * @var string $name Field name
     * @var string $value
     * @var boolean $disabled
     * @var string $placeholder
     * @var string $type
     */
?>
<input class="form-control" type="{{ $type }}" value="{{ $value }}" name="{{ $name }}"{!! isset($placeholder) ? ' placeholder="' . $placeholder . '"' : null !!}{!! $disabled ? ' disabled' : null !!}>