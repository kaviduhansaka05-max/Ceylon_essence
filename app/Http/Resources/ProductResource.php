<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /** @param \App\Models\Product $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->Product_ID ?? $this->id,
            'name'        => $this->name,
            'category'    => $this->category,
            'inventory'   => $this->inventory,
            'price'       => $this->price,
            'status'      => $this->status,
            'sold_pieces' => $this->sold_pieces,
            'created_at'  => $this->created_at,

            // relations
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
