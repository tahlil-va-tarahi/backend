<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
           'token' => $this->token,
           'trans_id' => $this->trans_id,
           'product' => Product::findOrFail($this->product_id)->title,
           'product_id' => $this->product_id,
           'user' => User::FindOrFail($this->user_id)->name,
           'user_id' => $this->user_id,
           'price' => Product::findOrFail($this->product_id)->price,
       ];
    }
}
