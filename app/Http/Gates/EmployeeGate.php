<?php
namespace App\Http\Gates;

class EmployeeGate{
    public function employeeGate($user){

          if($user->role ==='employee' OR $user->role ==='call-center' OR $user->role ==='super-admin' OR $user->role ==='accounts' OR $user->role ==='admin'){
                return true;
            }else{
                return false;
            }
    }
}
?>
