<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images=[];
        foreach ($this->place->media as $media){
            $images[] = ['id'=>$media->id,'url'=>$media->getUrl()];
        }
        return [
            'id'=>$this->id,
            'name'=>$this->place->name,
            'description' =>$this->place->description,
            'type' => $this->place->type,
            'phone' => $this->place->phone,
            'location' => $this->place->location,
            'rate' => $this->place->rate,
            'images' =>$images,
            'category'=>$this->place->category->id,
            'date' =>$this->date,
        ];
    }
}
