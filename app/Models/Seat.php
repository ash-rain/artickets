<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['section_id', 'row', 'column'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
