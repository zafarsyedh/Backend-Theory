<?php
namespace App\Http\Gates;

class AdminGate{
    public function adminGate($user){

          if($user->role ==='admin' OR $user->role ==='super-admin' OR $user->role_id=9){
                return true;
            }else{
                return false;
            }
    }
}
?>
