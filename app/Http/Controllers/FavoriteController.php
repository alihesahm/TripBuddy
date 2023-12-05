<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = currentUser();
        $places = currentUser()
            ->favorite()
            ->with('media')
            ->approved()
            ->get();
        return sendSuccessResponse(data:PlaceResource::collection($places));
    }


    public function store(Place $place)
    {
        currentUser()->favorite()->sync([$place->id], false);
        return sendSuccessResponse();
    }

    public function delete(Place $place)
    {
        currentUser()->favorite()->detach($place->id);
        return sendSuccessResponse();
    }
}
