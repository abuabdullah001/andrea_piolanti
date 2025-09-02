<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ItemResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'date'           => $this->date,
            'subtotal'       => $this->subtotal,
            'total'          => $this->total,
            'advance'        => $this->advance,
            'due'            => $this->due,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'status'         => $this->status,
            'service'        => new ServiceResource($this->whenLoaded('service')),
            'customer'       => new UserResource($this->whenLoaded('customer')),
            'owner'          => new UserResource($this->whenLoaded('owner')),
            'items'          => ItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
