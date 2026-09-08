<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRProfile extends Model
{
    use HasFactory;

    protected $table = 'qr_profiles';

    protected $fillable = [
        'user_id',
        'slug',
        'name',
        'designation',
        'company',
        'bio',
        'profile_image',
        'logo',
        'phone',
        'email',
        'website',
        'template_id',
        'theme_data',
        'status',
    ];

    protected $casts = [
        'theme_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class, 'profile_id')->orderBy('sort_order', 'asc');
    }

    public function customLinks()
    {
        return $this->hasMany(CustomLink::class, 'profile_id')->orderBy('sort_order', 'asc');
    }

    public function qrCode()
    {
        return $this->hasOne(QRCode::class, 'profile_id');
    }

    public function scans()
    {
        return $this->hasMany(QRScan::class, 'profile_id');
    }

    public function analyticsEvents()
    {
        return $this->hasMany(AnalyticsEvent::class, 'profile_id');
    }

    public function getPublicUrlAttribute(): string
    {
        return route('profile.show', $this->slug);
    }
}
