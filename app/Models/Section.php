<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['venue_id', 'name', 'price'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
