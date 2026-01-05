<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Subscription plan model.
 */
class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'trial_days',
        'features',
        'limits',
        'sort_order',
        'is_active',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Check if the plan has a specific feature.
     *
     * @param string $feature
     * @return bool
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return in_array('*', $features) || in_array($feature, $features);
    }

    /**
     * Get the limit value for a specific key.
     *
     * @param string $key
     * @return int|null Returns -1 for unlimited, null if not set
     */
    public function getLimit(string $key): ?int
    {
        return $this->limits[$key] ?? null;
    }
}
