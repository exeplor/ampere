<?php
    /**
     * @var \Ampere\Services\Workshop\Layout\Layout $layout Set name of target layout (can by "empty" or "default")
     * @var \Ampere\Services\Workshop\Form\Form $form
     * @var \Ampere\Services\Workshop\Component $component
     * @var object $data
     */

    $controllerClass = ampere_controller('Settings\RolesController');
    $permissionsControllerClass = ampere_controller('Settings\PermissionsController');
?>

@php($layout->title('Permissions'))

@php($component->show('header', [
    'title' => 'Permissions',
    'subtitle' => 'Admin permissions',
    'buttons' => [
        [
            'title' => 'Show roles',
            'route' => $controllerClass::route('index')
        ]
    ]
]))

<div class="ibox">
    <div class="ibox-body ibox-nopadding">
        <table class="table">
            <thead>
            <tr>
                <th>Permission</th>
                @foreach($data->roles as $role)
                    <th>{{ $role->title }}</th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            @foreach($data->list as $action => $data)
                <tr>
                    <td>
                        @if($data['title'])
                            {{ $data['title'] }}<br>
                            <small>{{ implode(' > ', $data['menu']) }}</small>
                        @else
                            {{ $action }}
                        @endif
                    </td>
                    @foreach($data['roles'] as $id => $status)
                        <td data-action="{{ $action }}" data-status="{{ $status === true ? 1 : 0 }}" data-role-id="{{ $id }}">
                            @if($status === true)
                                <button class="btn btn-success a-change-access">Allowed</button>
                            @else
                                <button class="btn btn-danger a-change-access">Disallowed</button>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $('.a-change-access').click(function(){
        var area = $(this).parent(),
            action = area.attr('data-action'),
            status = area.attr('data-status'),
            role_id = area.attr('data-role-id');

        status = status == 1 ? 0 : 1;

        var button = $(this);
        if (status === 1) {
            button.removeClass('btn-danger').addClass('btn-success').html('Allowed');
        } else {
            button.removeClass('btn-success').addClass('btn-danger').html('Disallowed');
        }

        area.attr('data-status', status);

        $.post('{{ $permissionsControllerClass::route('change') }}', {
            action: action,
            method: status === 1 ? 'attach' : 'detach',
            role_id: role_id,
            _token: '{{ csrf_token() }}'
        });
    });
</script>