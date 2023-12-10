<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserGenderEnum;
use Faker\Factory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'gender' => UserGenderEnum::class,
        'is_admin' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile')
            ->singleFile();
    }

    public function image():Attribute
    {
        return new Attribute(
            get: function (){
                $url = $this->getFirstMediaUrl('profile');
//                if (!$url){
//                    if ($this->gender == UserGenderEnum::MALE){
//                        return asset('male');
//                    }
//                    else{
//                        return asset('female');
//                    }
//                }
                return $url;
            }
        );
    }
    public function resetPassword(): HasOne
    {
        return $this->hasOne(ResetPassword::class);
    }

    public function places():HasMany
    {
        return $this->hasMany(Place::class);
    }

    public function favorite(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'favorites')->withTimestamps();
    }

    public function appointment():HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
