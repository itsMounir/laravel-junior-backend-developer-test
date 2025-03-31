<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends BaseModel
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'company_id',
    ];

    /**
     * Get the company that owns the inquiry.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
