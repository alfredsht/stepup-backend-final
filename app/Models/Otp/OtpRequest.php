<?php

namespace App\Models\Otp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpRequest extends Model
{
    use HasFactory;
    protected $table = 'otp_requests';
    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\LoginUser::class, 'id');
    }
}
