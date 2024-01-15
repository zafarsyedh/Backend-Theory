<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class RoleHasPermission extends Model
{
    use HasFactory;
    public function permName()
    {
        return $this->hasOne(Permission::class, 'id', 'permission_id')->select(['id','name','route','icon']);
    }

    public function permData()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id')->select(['id','name']);
    }
}
