<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentSearch extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'keyword'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
