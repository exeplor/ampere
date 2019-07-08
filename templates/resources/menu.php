<?php

return [

    'Home' => [
        'title' => __('Home'),
        'icon' => 'home',
    ],

    'Settings' => [
        'title' => __('Settings'),
        'icon' => 'cog',
        'child' => [
            'Permissions' => __('Permissions'),
            'Users' => __('Users'),
            'Roles' => __('Roles')
        ]
    ],

    'Logout' => [
        'title' => __('Logout'),
        'icon' => 'sign-out-alt',
    ],

];