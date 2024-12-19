<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        // php artisan shield:install admin
        // php artisan shield:generate --all
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions =
        '[
            {"name":"User","guard_name":"web","permissions":[]},
            {"name":"super_admin","guard_name":"web","permissions":[
            "view_any_shield::role",
            "create_shield::role",
            "update_shield::role",
            "delete_shield::role",
            "view_strategic::element",
            "view_any_strategic::element",
            "create_strategic::element",
            "update_strategic::element",
            "delete_strategic::element",
            "view_strategy","view_any_strategy",
            "create_strategy","update_strategy",
            "delete_strategy","view_any_user",
            "create_user","update_user","delete_user"
            ]},
            {"name":"admin","guard_name":"web","permissions":[
            "view_any_shield::role","create_shield::role",
            "update_shield::role","delete_shield::role",
            "view_strategic::element","view_any_strategic::element"
            ,"create_strategic::element","update_strategic::element",
            "delete_strategic::element","view_strategy","view_any_strategy",
            "create_strategy","update_strategy","delete_strategy",
            "view_any_user","create_user","update_user","delete_user"
            ]},
            {"name":"panel_user","guard_name":"web","permissions":[
            "view_any_shield::role","create_shield::role",
            "update_shield::role","delete_shield::role",
            "view_strategic::element","view_any_strategic::element"
            ,"create_strategic::element","update_strategic::element",
            "delete_strategic::element","view_strategy","view_any_strategy",
            "create_strategy","update_strategy","delete_strategy",
            "view_any_user","create_user","update_user","delete_user"
            ]}
        ]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
