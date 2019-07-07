<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Layout $layout
     * @var \Ampere\Services\Workshop\Component $component
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Form\Form $form
     * @var object $data
     */

    $controllerClass = ampere_controller('Settings\UsersController');
?>

@php($layout->title('Users'))
@php($component->show('header', [
    'title' => 'Users',
    'subtitle' => 'List of Users',
    'buttons' => [
        'create' => [
            'title' => 'Create new User',
            'route' => $controllerClass::route('create'),
            'type' => 'primary'
        ]
    ]
]))

<div class="ibox">
    <div class="ibox-body ibox-nopadding">
        @php($component->grid($data->grid))
    </div>
</div>