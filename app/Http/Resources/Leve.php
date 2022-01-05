<?php

namespace App\Http\Resources;

use App\Http\Resources\Recipe as RecipeResource;
use App\Http\Resources\Location as LocationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Leve extends JsonResource
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
            'id'       => $this->id,
            'name'     => $this->name,
            // 'item'     => new ItemResource($this->requirements->first()),
            'recipe'   => new RecipeResource($this->requirements->first()->recipes->first()),
            'quantity' => $this->requirements[0]->pivot->amount,
            'level'    => $this->level,
            'xp'       => $this->xp,
            'gil'      => $this->gil,
            'repeats'  => $this->repeats,
            'frame'    => icon($this->frame),
            'plate'    => icon($this->plate),
            'location' => new LocationResource($this->location),
        ];
    }
}
