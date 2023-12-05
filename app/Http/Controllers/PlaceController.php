<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index(?Category $category=null)
    {
        $user = currentUser();
        $places = is_null($category)
            ? Place::approved()->byThisUser($user)->with('media')->get()
            : $category->places()->approved()->byThisUser($user)->with('media')->get();

        return sendSuccessResponse(data:PlaceResource::collection($places));
    }

    public function store(StorePlaceRequest $request)
    {
        $data = $request->validated();
        $place = currentUser()->places()->create($data);
        foreach ($request->images as $image){
            $place->addMedia($image)->toMediaCollection();
        }
        return sendSuccessResponse();
    }

    public function update(Place $place,UpdatePlaceRequest $request)
    {
        $data = $request->validated();
        $place->update($data);
        return sendSuccessResponse();
    }

    public function approve(Place $place)
    {
        $place->update(['is_approved'=>!$place->is_approved]);
        return sendSuccessResponse();
    }

    public function adminIndex(Request $request)
    {
        $data = $request->validate([
            'is_approved'=>['nullable','boolean']
        ]);
        $places = Place::query()
            ->when(!is_null($request->is_approved),function ($query) use($request){
                return $query->where('is_approved',$request->is_approved);
            })->get();

        return sendSuccessResponse(data:PlaceResource::collection($places));
    }

}
