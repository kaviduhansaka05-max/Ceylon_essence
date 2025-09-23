<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /** @param \App\Models\Order $resource */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->OrderID ?? $this->id,
            'customer_id'=> $this->customer_id ?? $this->CustomerID,
            'status'    => $this->status,
            'location'  => $this->location,
            'total'     => $this->total,
            'quantity'  => $this->quantity,
            'created_at'=> $this->created_at,

            // relations
            'customer'   => new CustomerResource($this->whenLoaded('customer')),
            'items'      => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'products'   => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
