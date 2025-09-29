<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /** @param \App\Models\Customer $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->CustomerID ?? $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'user_type'  => $this->user_type ?? 'customer',
            'created_at' => $this->created_at,

            // relations (included only if eager-loaded)
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}
