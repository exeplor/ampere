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
    'title' => isset($data->model) ? 'Update Role #' . $data->model->id : 'Create new Role',
    'subtitle' => 'Role form',
    'buttons' => [
        'create' => [
            'title' => 'List of Roles',
            'route' => $controllerClass::route('index'),
        ]
    ]
]))

<div class="row">
    <div class="col-md-6">
        <div class="ibox">
            <div class="ibox-body">
                {!! $form->open()->model($data->model ?? null) !!}

                    {!! $form->input('title', 'Title')
							->placeholder('Enter title')
					!!}

					{!! $form->input('description', 'Description')
							->placeholder('Enter description')
					!!}

					{!! $form->input('alias', 'Alias')
							->placeholder('Enter alias')
					!!}

                    <button class="btn btn-primary">{{ isset($data->model) ? 'Update' : 'Create' }}</button>

                {!! $form->close() !!}
            </div>
        </div>
    </div>
</div>