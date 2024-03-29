<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Place extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'phone',
        'location',
        'category_id',
        'type',
        'rate',
        'is_approved',
    ];

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved',true);
    }

    public function scopeIsFavorites(Builder $query,User $user): Builder
    {
        return $query->withExists(['favorites as is_favorites' => function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        }]);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function userHowFavorite(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function appointment():HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
