<?php

namespace App\Models;


use Spatie\Permission\Models\Role;

class UserRoles extends Role
{
    public function user()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

}
