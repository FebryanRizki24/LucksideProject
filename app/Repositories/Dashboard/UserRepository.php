<?php

namespace App\Repositories\Dashboard;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserRepository
{
    public function getDatatables()
    {
        $query = User::query();

        return DataTables::of($query)
            ->addColumn('role', function ($row) {
                return $row->getRoleNames()->implode(', ');
            })
            ->make(true);
    }

    public function getAllUsers()
    {
        return User::get();
    }

    public function getRole()
    {
        return Role::get();
    }

    public function create($data)
    {
        $data['password'] = Hash::make('password');

        $user = User::create($data);

        if (isset($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return $user;
    }


    public function update($id, $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }
}
