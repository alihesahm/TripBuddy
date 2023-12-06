<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images=[];
        foreach ($this->media as $media){
            $images[] = ['id'=>$media->id,'url'=>$media->getUrl()];
        }
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'description' =>$this->description,
            'type' => $this->type,
            'phone' => $this->phone,
            'location' => $this->location,
            'rate' => $this->rate,
            'images' =>$images,
            'is_favorites' => $this->is_favorites,
        ];
    }
}
