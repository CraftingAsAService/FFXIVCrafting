<?php

namespace App\Http\Resources;

use App\Http\Resources\Category as CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
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
            'id'          => $this->id,
            'name'        => $this->name,
            'price'       => $this->price,
            'gc_price'    => $this->gc_price,
            'special_buy' => !! $this->special_buy,
            'tradeable'   => $this->tradeable,
            'ilvl'        => $this->ilvl,
            'category'    => new CategoryResource($this->category),
            'rarity'      => $this->rarity,
            'icon'        => icon($this->icon),
            // 'recipes'     => $this->recipes->pluck('id')->toArray(),
        ];
        return parent::toArray($request);
    }
}
