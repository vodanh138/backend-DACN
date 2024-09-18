<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\Show;
use App\Models\User;
use App\Models\Role;
use App\Models\Section;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class initial extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'username' => 'admin',
            'password' => Hash::make('123456'),
            'lastname' => '',
            'firstname' => 'admin',
            'name' => 'admin',
            'ava' => '/images/default-ava.png',
            'coverphoto' => '/images/default-coverphoto.png',
        ]);
        $role = Role::create(['name' => 'admin']);
        $role = Role::create(['name' => 'user']);
        $user->roles()->attach($role->id);
    }
}
