<?php
/**
 * @var \Ampere\Services\Workshop\Page\Layout $layout
 * @var \Ampere\Services\Workshop\Component $component
 * @var \Ampere\Services\Workshop\Page\Assets $include
 * @var \Ampere\Services\Workshop\Form\Form $form
 * @var object $data
 */

$layout->set('empty')->title('Ampere Login');
?>

<div id="auth-area">
    <div class="ibox">
        <div class="ibox-body">
            <h1>AMPERE</h1>
            {!! $form->open()->post() !!}

            <div class="form-group">
                {!!
                    $form->input('email')
                        ->placeholder('Enter email')
                !!}
            </div>

            <div class="form-group">
                {!!
                    $form->input('password')
                        ->type('password')
                        ->placeholder('Enter password')
                !!}
            </div>

            @if(ampere_config('auth.google_captcha.enabled'))
                <hr>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="{{ ampere_config('auth.google_captcha.site_key') }}"></div>
                </div>
            @endif

            <hr>
            <div align="center">
                <button type="submit" class="btn btn-success">
                    Login
                </button>
            </div>

            {!! $form->close() !!}
        </div>
    </div>
</div>

@if(ampere_config('auth.google_captcha.enabled'))
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endif