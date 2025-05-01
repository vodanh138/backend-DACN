<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function model(): string
    {
        return User::class;
    }
    public function findLoggedUser()
    {
        return $this->model->find(Auth::id());
    }
    public function getUserByName($name)
    {
        return $this->model->where('username', $name)->first();
    }
    public function createUser(
        $username,
        $password
    ) {
        return $this->model->create(
            [
                'username' => $username,
                'password' => Hash::make($password),
                'lastname' => 'new',
                'firstname'=> 'user',
                'name'=> 'new user',
                'ava' => 'default-ava_qijax6.png',
                'coverphoto' => 'default-coverphoto_ol32t6.png',
            ]
        );
    }
    public function getUsersHaveName($name)
    {
        return $this->model->where('name', 'like', "%{$name}%")->where('name', '!=', 'admin')->get();
    }
    public function getUserById($id)
    {
        return $this->model->where('id', $id)->first();
    }
}
