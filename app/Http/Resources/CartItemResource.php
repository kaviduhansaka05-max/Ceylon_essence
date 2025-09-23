<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /** @param \App\Models\CartItem $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id ?? $this->CartItemID ?? null,
            'cart_id'    => $this->cart_id ?? $this->CartID,
            'product_id' => $this->product_id ?? $this->Product_ID,
            'quantity'   => $this->quantity,
            'price'      => $this->price,
            'total'      => $this->total,

            // relations
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
