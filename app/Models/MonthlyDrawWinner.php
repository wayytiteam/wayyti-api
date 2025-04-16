<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MonthlyDrawWinner extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'has_claimed',
        'email',
        'country',
        'gift_card'
    ];

    protected function casts(): array
    {
        return [
            'has_claimed' => 'boolean'
        ];
    }

    public function notification(): HasOne
    {
        return $this->hasOne(Notification::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
