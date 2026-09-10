<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'name',
        'email',
        'phone',
        'message',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
