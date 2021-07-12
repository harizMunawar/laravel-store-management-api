<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CategoryProduct;
use App\Models\Store;
use App\Http\Resources\StoreResource;
use App\Http\Resources\CategoryResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'description'=> $this->description,
            'price'=> $this->price,
            'stock'=> $this->stock,
            'category'=> CategoryResource::collection($this->categories),
            'store'=> new StoreResource(Store::find($this->store_id))
        ];
    }
}
