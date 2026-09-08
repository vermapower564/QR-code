<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRScan extends Model
{
    use HasFactory;

    protected $table = 'qr_scans';

    protected $fillable = [
        'profile_id',
        'scanned_at',
        'ip_hash',
        'country',
        'region',
        'city',
        'device',
        'os',
        'browser',
        'referrer',
        'user_agent',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
