<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleProduct extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'title',
        'image',
        'merchant',
        'original_price',
        'latest_price',
        'currency',
        'country',
        'description',
        'link',
        'job_id'
    ];


    protected $appends = ['latest_price_str', 'original_price_str'];

    public function tracked_products(): HasMany
    {
        return $this->hasMany(TrackedProduct::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tracked_products', 'google_product_id', 'user_id');
    }

    public function getLatestPriceStrAttribute() {
        return $this->currency.$this->latest_price;
    }

    public function getOriginalPriceStrAttribute() {
        return $this->currency.$this->original_price;
    }
}
