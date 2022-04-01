<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'url' => $this->url,
            'quantity' => $this->quantity,
            'category' => $this->category,
            'image_url' => $this->image ? url("storage/$this->image") : '',
            'specifications' => SpecificationResource::collection($this->specifications)
        ];
    }
}
