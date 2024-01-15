<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes,HasFactory;

    public function permissions()
    {
        return $this->hasMany(RoleHasPermission::class, 'role_id', 'id');
    }


//    public function getPermissionsViaRoles()
//    {
//        return $this->hasMany(RoleHasPermission::class, 'role_id', 'id');
//    }
}
