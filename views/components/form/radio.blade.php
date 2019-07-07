<?php
    /**
     * @var string $name Field name
     * @var string $title
     * @var string $value
     * @var string $error
     */
?>

<div class="radio-group">
    @foreach($items as $id => $title)
        <div class="form-check">
            <label>
                <input type="radio" name="{{ $name }}"{{ $id == $value ? ' checked' : null }}> <span class="label-text">{{ $title }}</span>
            </label>
        </div>
    @endforeach
</div>
