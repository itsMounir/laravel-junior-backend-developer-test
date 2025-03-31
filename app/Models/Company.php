<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'owner_id',
        'country_id',
        'industry_id',
        'phone_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'phone_verified_at'
    ];

    protected $appends = [
        'country_name',
        'industry_name',
        'created_from',
    ];

    public function getCountryNameAttribute()
    {
        return $this->country()->pluck('name')->first();
    }

    public function getIndustryNameAttribute()
    {
        return $this->industry()->pluck('name')->first();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
