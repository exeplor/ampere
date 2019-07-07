<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Layout $layout
     * @var \Ampere\Services\Workshop\Component $component
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Form\Form $form
     * @var object $data
     */
?>

@php($layout->title('Ampere Home'))

@php($component->show('header', [
    'title' => 'Dashboard',
    'subtitle' => 'Production dashboard',
    'buttons' => [
        'create' => [
            'title' => 'Create new user',
            'action' => 'http://googe.com',
            'type' => 'primary'
        ],
        'show' => [
            'title' => 'Show payments',
            'action' => 'http://googe.com',
            'type' => 'success'
        ]
    ]
]))

<div class="row">
    <div class="col-md-6">
        <div class="ibox">
            <div class="ibox-header">
                <h4>Buttons</h4>
            </div>
            <div class="ibox-content">
                <a href="#" class="btn btn-primary">
                    Primary button
                </a>

                <a href="#" class="btn btn-success">
                    Success button
                </a>

                <a href="#" class="btn btn-danger">
                    Danger button
                </a>

                <a href="#" class="btn btn-warning">
                    Warning button
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ibox">
            <div class="ibox-header">
                Buttons
            </div>
            <div class="ibox-content">
                <a href="#" class="btn btn-primary">
                    Primary button
                </a>

                <a href="#" class="btn btn-success">
                    Success button
                </a>

                <a href="#" class="btn btn-danger">
                    Danger button
                </a>

                <a href="#" class="btn btn-warning">
                    Warning button
                </a>
            </div>
        </div>
    </div>
</div>

