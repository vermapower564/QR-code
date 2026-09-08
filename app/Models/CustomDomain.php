<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'domain',
        'domain_type',
        'verification_token',
        'verification_status',
        'ssl_status',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(QRProfile::class);
    }
}
