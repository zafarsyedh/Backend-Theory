<?php
namespace App\Http\Gates;

class CallCenterGate{
    public function callcenterGate($user){

          if($user->role ==='call-center' OR $user->role ==='super-admin' OR $user->role_id===9 OR $user->role_id===7 OR $user->role_id===4){
                return true;
            }else{
                return false;
            }
    }
}
?>
