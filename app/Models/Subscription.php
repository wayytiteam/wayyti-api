<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'type'
    ];

    protected $appends = ['server_time'];

    public function getServerTimeAttribute()
    {
        return Carbon::parse(now())->toDateTimeString();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
