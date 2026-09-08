<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'title',
        'description',
        'url',
        'icon',
        'sort_order',
        'status',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
