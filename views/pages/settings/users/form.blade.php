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
    'title' => isset($data->model) ? 'Update User #' . $data->model->id : 'Create new User',
    'subtitle' => 'User form',
    'buttons' => [
        'create' => [
            'title' => 'List of Users',
            'route' => $controllerClass::route('index'),
        ]
    ]
]))

<div class="row">
    <div class="col-md-6">
        <div class="ibox">
            <div class="ibox-body">
                {!! $form->open()->model($data->model ?? null) !!}

                    {!! $form->input('name', 'Name')
							->placeholder('Enter name')
					!!}

					{!! $form->input('email', 'Email')
							->placeholder('Enter email')
					!!}

					{!! $form->input('password', 'Password')
							->placeholder('Enter password')->nullable()
					!!}

					{!! $form->select('roles', 'Roles')
							->placeholder('Select roles')
							->multiple()
							->source($controllerClass::route('search'))
					!!}

                    <button class="btn btn-primary">{{ isset($data->model) ? 'Update' : 'Create' }}</button>

                {!! $form->close() !!}
            </div>
        </div>
    </div>
</div>