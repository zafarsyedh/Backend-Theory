<?php
namespace App\Repo\Interfaces;

interface RoleInterface{

    public function getAllRoles();
    public function saveRole($request);
    public function saveRolePermissions($request);
    public function deleteRole($id);

    public function editRole($id);
    public function updateRole($request);
    public function getAllPermissions($id);

}
