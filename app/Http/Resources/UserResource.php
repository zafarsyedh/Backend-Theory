<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'user_type' => 1,
            'user_id' => $this->id,
            'branch_id' => $this->branch_id,
            'branch' => $this->branch,
            'api_token' => $this->api_token,
            'name'      => $this->name,
            'image'      => $this->image,
            'email'     => $this->email,
            'token'     => $this->createToken("Token")->plainTextToken,
            'permissions'=> User::with('role.permissions')->select('role_id')->find($this->id)
        ];
    }
}
