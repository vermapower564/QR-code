<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'secret',
        'events',
        'status',
    ];

    protected $casts = [
        'events' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
