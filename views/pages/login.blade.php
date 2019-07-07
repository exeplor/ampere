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
            {!! $form->open()->post() !!}

                {!!
                    $form->input('email', 'Email')
                        ->placeholder('Enter email')
                !!}

                {!!
                    $form->input('password', 'Password')
                        ->type('password')
                        ->placeholder('Enter password')
                !!}

                @if(ampere_config('auth.google_captcha.enabled'))
                    <div class="form-group">
                        <label class="control-label">Validation</label>
                        <div class="g-recaptcha" data-sitekey="{{ ampere_config('auth.google_captcha.site_key') }}"></div>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">Login</button>

            {!! $form->close() !!}
        </div>
    </div>
</div>

@if(ampere_config('auth.google_captcha.enabled'))
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endif