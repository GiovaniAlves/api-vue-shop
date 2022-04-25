<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total' => $this->total,
            'status' => $this->status,
            'status_label' => $this->statusOptions[$this->status],
            'date' => Carbon::make($this->created_at)->format('d/m/Y'),
            'date_time' => Carbon::make($this->created_at)->format('d/m/Y H:m:i'),
            'orderProducts' => OrderProductResource::collection($this->orderProducts($this->id)),
            'client' => new UserResource($this->client),
            // products' => ProductResource::collection($this->products),
        ];
    }
}
