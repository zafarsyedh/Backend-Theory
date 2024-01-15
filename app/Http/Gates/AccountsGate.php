<?php
namespace App\Http\Gates;

class AccountsGate{
    public function accountGate($user){

          if($user->role ==='accounts' OR $user->role ==='super-admin' OR $user->role_id===10 ){
                return true;
            }else{
                return false;
            }
    }
}
?>
