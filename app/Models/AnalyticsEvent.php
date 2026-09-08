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
        'user_agent_hash',
        'country',
        'region',
        'city',
        'device',
        'os',
        'browser',
        'referrer',
        'is_bot',
        'occurred_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_bot' => 'boolean',
        'occurred_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
