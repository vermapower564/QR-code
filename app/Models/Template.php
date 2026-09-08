<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'preview_image',
        'template_data',
        'is_premium',
        'status',
    ];

    protected $casts = [
        'template_data' => 'array',
        'is_premium' => 'boolean',
    ];

    public function qrProfiles()
    {
        return $this->hasMany(QRProfile::class);
    }
}
