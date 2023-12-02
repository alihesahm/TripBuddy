<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResetPassword extends Model
{
    use HasFactory;

    protected $primaryKey = 'email';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'otp',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
