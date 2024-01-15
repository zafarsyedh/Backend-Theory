<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionModule extends Model
{
    use HasFactory;

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id', 'id')->select(['id','module_id','name']);
    }

    public static function getModuleWithPermName(){
        return $res=PermissionModule::with('permissions')->get();
    }
}
