<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /** @param \App\Models\User $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id, // default Jetstream pk
            'name'       => $this->name,
            'email'      => $this->email,
            'photo_url'  => $this->when(isset($this->profile_photo_url), $this->profile_photo_url),
            'created_at' => $this->created_at,
        ];
    }
}
