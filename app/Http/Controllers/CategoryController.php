<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\PlaceResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)
    {
        $user = currentUser();
        $places = $category->places()->approved()->with('media')->withExists(['userHowFavorite as is_favorites' => function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();
        return sendSuccessResponse(data:PlaceResource::collection($places));

    }

}
