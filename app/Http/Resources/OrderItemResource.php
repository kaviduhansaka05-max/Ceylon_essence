<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /** @param \App\Models\OrderItem $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id ?? $this->OrderItemID ?? null,
            'order_id'  => $this->order_id ?? $this->OrderID,
            'product_id'=> $this->product_id ?? $this->Product_ID,
            'quantity'  => $this->quantity,

            // relation (optional)
            'product'   => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
