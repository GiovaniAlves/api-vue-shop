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
            'description' => $this->description,
            'short_description' => $this->strLimitWords($this->description, 30),
            'price' => $this->price,
            'url' => $this->url,
            'quantity' => $this->quantity,
            'category' => $this->category,
            'category_label' => $this->categoryOptions[$this->category],
            'image_url' => $this->image ? url("storage/$this->image") : '',
            'specifications' => SpecificationResource::collection($this->specifications)
        ];
    }

    /**
     * @param string $string
     * @param int $limit
     * @param string $pointer
     * @return string
     */
    function strLimitWords(string $string, int $limit, string $pointer = "..."): string
    {
        $string = trim(filter_var($string, FILTER_SANITIZE_STRING));
        $arrWords = explode(" ", $string);
        $numWords = count($arrWords);

        if ($numWords < $limit) {
            return $string;
        }

        $words = implode(" ", array_slice($arrWords, 0, $limit));
        return "{$words}{$pointer}";
    }
}
