<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlaceController extends Controller
{
    public function index()
    {
        $user = currentUser();
        $places = Place::approved()->with('media')->withExists(['userHowFavorite as is_favorites' => function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();
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

    public function show(Place $place)
    {
        if (!$place->is_approved) {
            throw new NotFoundHttpException();
        }
        $place->loadExists(['userHowFavorite as is_favorites' => function ($query) {
            $query->where('user_id',currentUser()->id);
        }]);
        return sendSuccessResponse(data:PlaceResource::make($place));
    }

}
