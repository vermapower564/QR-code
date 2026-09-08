<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSlugHistory extends Model
{
    use HasFactory;

    protected $table = 'profile_slug_histories';

    protected $fillable = [
        'profile_id',
        'old_slug',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
