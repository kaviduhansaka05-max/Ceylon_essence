<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingCartResource extends JsonResource
{
    /** @param \App\Models\ShoppingCart $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->CartID ?? $this->id,
            'customer_id' => $this->customer_id ?? $this->CustomerID,
            'total_price' => $this->total_price,
            'created_at'  => $this->created_at,

            // relations
            'items'    => CartItemResource::collection($this->whenLoaded('cartItems')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
