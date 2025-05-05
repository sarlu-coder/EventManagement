<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendee extends Model
{
    use HasFactory;

    protected $fillable = ['name','email','country','booking_id'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
