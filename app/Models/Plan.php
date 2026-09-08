<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'billing_cycle',
        'profile_limit',
        'link_limit',
        'storage_limit',
        'status',
    ];

    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getFeatureValue(string $key, $default = null)
    {
        $feature = $this->features->firstWhere('feature_key', $key);
        return $feature ? $feature->feature_value : $default;
    }

    public function hasFeature(string $key): bool
    {
        $val = $this->getFeatureValue($key);
        return $val === '1' || $val === 'true' || $val === true;
    }
}
