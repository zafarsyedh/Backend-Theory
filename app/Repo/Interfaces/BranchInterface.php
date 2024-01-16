<?php
namespace App\Repo\Interfaces;

interface BranchInterface{

    public function getAllBranches();
    public function createBranch($request);
    public function deleteBranch($id);
    public function getAllBranchForDropdown();


}
