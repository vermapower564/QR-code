<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRCode extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';

    protected $fillable = [
        'profile_id',
        'format',
        'file_path',
        'foreground_color',
        'background_color',
        'style',
        'logo_path',
    ];

    public function profile()
    {
        return $this->belongsTo(QRProfile::class, 'profile_id');
    }
}
