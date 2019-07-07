<?php

namespace Ampere\Commands;

use Ampere\Models\Permission;
use Ampere\Models\Role;
use Ampere\Models\RolePermission;
use Ampere\Models\User;
use Ampere\Services\PermissionService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ampere:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install ampere system';

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * @var array
     */
    private $userList = [
        [
            'name' => 'Admin',
            'email' => 'admin@ampere',
            'password' => 'admin',
            'role' => 'admin'
        ]
    ];

    /**
     * Create a new command instance.
     *
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();

        $this->permissionService = $permissionService;
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        Artisan::call('migrate');

        $this->createRoles();
        $this->createPermissions();
        $this->createRolePermissions();
        $this->createUsers();

        $this->info('Migration completed');
    }

    /**
     * Create default roles
     */
    private function createRoles()
    {
        $roles = [
            [
                'title' => 'Admin',
                'description' => 'Has full access to functionality',
                'alias' => 'admin',
            ]
        ];

        foreach($roles as $role) {
            Role::where('alias', $role['alias'])->firstOrCreate($role);
        }

        $this->comment('Roles created');
    }

    /**
     * Create default permissions
     */
    private function createPermissions()
    {
        $permissions = [
            [
                'title' => 'Full access',
                'action' => '*'
            ]
        ];

        foreach($permissions as $permission) {
            Permission::where('action', $permission['action'])->firstOrCreate($permission);
        }

        $this->comment('Permissions created');
    }

    /**
     * Create default role permissions
     */
    private function createRolePermissions()
    {
        $rolePermissions = [
            'admin' => ['*']
        ];

        $roles = Role::get()->keyBy('alias');
        $permissions = Permission::get()->keyBy('action');

        foreach($rolePermissions as $roleName => $permissionList) {
            foreach($permissionList as $permissionName) {
                $params = [
                    'role_id' => $roles[$roleName]->id,
                    'permission_id' => $permissions[$permissionName]->id
                ];

                RolePermission::where($params)->firstOrCreate($params);
            }
        }

        $this->comment('Role permissions created');
    }

    /**
     * Create default users
     *
     * @throws \Exception
     */
    private function createUsers()
    {
        $userList = $this->userList;

        foreach($userList as $userData) {
            $this->info('------------------------');
            $roleEntity = Role::where('alias', $userData['role'])->first();

            if (User::where('email', $userData['email'])->count()) {
                $this->info('- Use exists user');

            } else {
                $this->info('- Create new user');

                $userEntity = User::create(array_merge($userData, [
                    'password' => Hash::make($userData['password'])
                ]));

                $this->permissionService->attachRoleToUser($roleEntity, $userEntity);
            }

            $this->comment('   Email: ' . $userData['email']);
            $this->comment('   Password: ' . $userData['password']);
            $this->comment('   Role: ' . $roleEntity->title . PHP_EOL);
        }
    }
}
