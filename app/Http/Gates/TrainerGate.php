<?php
namespace App\Http\Gates;

class TrainerGate{
    public function checkTrainer($user){



          if($user->role ==='trainer' OR $user->role ==='super-admin'){
                return true;
            }else{
                return false;
            }
    }
}
?>
