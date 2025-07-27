<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['seat_id', 'name', 'email', 'phone'];

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
