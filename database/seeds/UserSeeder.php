<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'get-users']);
        Permission::create(['name' => 'add-users']);
        Permission::create(['name' => 'del-users']);

        $this->seedAdmin();
        $this->seedUser();
    }


    protected function seedAdmin()
    {
        /** @var Role $role */
        $role = Role::create(['name' => 'admin']);

        $role->syncPermissions([1,2,3]);

        /** @var User $user */
        $user = User::create([
            'status' => User::STATUS_ACTIVE,
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password')
        ]);

        $user->syncRoles([1]);
    }

    protected function seedUser()
    {
        /** @var Role $role */
        $role = Role::create(['name' => 'user']);

        $role->syncPermissions([1]);

        /** @var User $user */
        $user = User::create([
            'status' => User::STATUS_ACTIVE,
            'name' => 'User',
            'email' => 'user@user.com',
            'password' => Hash::make('password')
        ]);

        $user->syncRoles([2]);
    }
}
