<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'reporter_ip_hash',
        'reason',
        'description',
        'status',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
