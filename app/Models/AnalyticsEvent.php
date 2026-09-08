<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'event_type',
        'link_id',
        'ip_hash',
        'country',
        'device',
        'browser',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
