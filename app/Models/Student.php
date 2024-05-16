<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Student extends Model
{
    use HasFactory, Notifiable,HasRoles,HasApiTokens,SoftDeletes;


    protected $fillable=['traffic_id','std_name','email','password','std_gender','geartype','language','branch','mobile_no','progress','brcode','coursetype','prefferd_golden_chance'
    ,'historycls','pendingamount','paidamount'
    ];


    public function activeCourse()
    {
        return $this->hasOne(StudentCourse::class,  'std_id','id')->where('is_active',1);
    }





}
