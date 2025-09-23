<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /** @param  \App\Models\Admin  $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,           // or AdminID if you have a custom pk
            'name'      => $this->name,
            'email'     => $this->email,
            'user_type' => $this->user_type,
            'created_at'=> $this->created_at,
        ];
    }
}
