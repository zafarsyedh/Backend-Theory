<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{

    protected $stdData;

    public function __construct($resource, $stdData)
    {
        parent::__construct($resource);
        $this->stdData = $stdData;
    }
    public function toArray($request)
    {
        return [

            'user_id' => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'stdInfo'=> $this->stdData,
            'user_type' => 2,
            'api_token' => $this->api_token,
            'supervisor_id'      => $this->id,
            'supervisor_name'      => $this->name,
            'supervisor_email'     => $this->email,
            'token'     => $this->createToken("Token")->plainTextToken,
            'roles'     => $this->roles->pluck('name') ?? [],
            'roles.permissions' => $this->getPermissionsViaRoles()->pluck(['name']) ?? [],
            'permissions'=> $this->permissions->pluck('name') ?? []



        ];
    }
}
