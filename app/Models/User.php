<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasRoles,HasApiTokens,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','phone', 'password','role_id','branch_id','status','room_id'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:d M Y h:i:s a',
    ];

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id', 'id')->select(['id','name']);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->select(['id','title','exam_template']);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id')->select(['id','title','branch_id','room_purpose']);
    }

    /* public function getStatusAttribute($value)
     {
         if($value==1){
             $getVal='Active';
         }
         if($value==2){
             $getVal='In-Active';
         }
         return $getVal;
     }*/







}
