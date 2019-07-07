<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Layout $layout
     * @var \Ampere\Services\Workshop\Component $component
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Form\Form $form
     * @var object $data
     */

    $controllerClass = ampere_controller('Settings\RolesController');
?>

@php($layout->title('Roles'))
@php($component->show('header', [
    'title' => 'Roles',
    'subtitle' => 'List of Roles',
    'buttons' => [
        'create' => [
            'title' => 'Create new Role',
            'route' => $controllerClass::route('create'),
            'type' => 'success'
        ]
    ]
]))

<div class="ibox">
    <div class="ibox-body ibox-nopadding">
        @php($component->grid($data->grid))
    </div>
</div>