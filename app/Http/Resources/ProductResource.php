<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
			'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail_url' => "http://localhost:8000/".$this->thumbnail_url,
            'source_url' => "http://localhost:8000/".$this->source_url,
            'price' => $this->price,
            'category' => Category::findOrFail($this->category_id),
        ];
    }
}
