<?php
namespace App\Http\Gates;

class HrGate{
    public function checkHr($user){

          if($user->role ==='hr' OR $user->role ==='super-admin'){
                return true;
            }else{
                return false;
            }
    }
}
?>
