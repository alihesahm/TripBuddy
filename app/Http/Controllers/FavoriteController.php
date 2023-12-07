<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = currentUser();
        $places = currentUser()
            ->favorite()
            ->with('media')
            ->approved()
            ->withExists(['userHowFavorite as is_favorites' => function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();
        return sendSuccessResponse(data:PlaceResource::collection($places));
    }


    public function store(Place $place)
    {
        if(!$place->is_approved){
            throw new NotFoundHttpException();
        }
        currentUser()->favorite()->sync([$place->id], false);
        return sendSuccessResponse('add successfully');
    }

    public function delete(Place $place)
    {
        if(!$place->is_approved){
            throw new NotFoundHttpException();
        }
        currentUser()->favorite()->detach($place->id);
        return sendSuccessResponse('deleted successfully');
    }
}
