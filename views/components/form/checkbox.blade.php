<?php
    /**
     * @var string $name Field name
     * @var string $title
     * @var string $value
     * @var string $error
     */
?>

<div class="radio-group checkbox-group">
    <div class="form-check">
        <label>
            <input type="checkbox" name="{{ $name }}"{{ $checked ? ' checked' : null }}> <span class="label-text">{{ $title }}</span>
        </label>
    </div>
</div>
