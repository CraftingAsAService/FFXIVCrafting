<?php

namespace App\Http\Resources;

use App\Http\Resources\Item as ItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Recipe extends JsonResource
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
            'id'           => $this->id,
            'job_id'       => $this->job_id,
            'item'         => new ItemResource($this->item),
            'recipe_level' => $this->recipe_level,
            'stars'        => $this->stars,
            'yield'        => $this->yield,
            // 'reagents'     => $this->reagents->mapWithKeys(function($row) {
            //     return [$row->id => $row->pivot->amount];
            // }),
        ];
    }
}
