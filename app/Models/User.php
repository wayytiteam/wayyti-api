<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'email_verified_at',
        'facebook_id',
        'google_id',
        'ios_id',
        'password',
        'country',
        'age_group',
        'fcm_token',
        'is_admin',
        'has_reviewed'
    ];

    protected $appends = ['completed','status','total_entries'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_reviewed' => 'boolean'
        ];
    }

    public function personas(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class);
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function tracked_products(): HasMany
    {
        return $this->hasMany(TrackedProduct::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class);
    }

    public function recent_searches(): HasMany
    {
        return $this->hasMany(RecentSearch::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    // public function google_products(): BelongsToMany
    // {
    //     return $this->belongsToMany(GoogleProduct::class, 'tracked_products', 'user_id', 'google_product_id');
    // }

    public function monthly_draws(): HasMany
    {
        return $this->hasMany(MonthlyDraw::class);
    }

    public function getCompletedAttribute()
    {
        return $this->username && $this->country && $this->age_group ? true : false;
    }

    public function getTotalEntriesAttribute()
    {
        return $this->monthly_draws()->sum('entries');
    }

    public function getStatusAttribute()
    {
        $has_recent_attendance = $this->attendances()
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->exists();

        return $has_recent_attendance ? 'active' : 'inactive';
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }
}
