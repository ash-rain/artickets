<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = ['name'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
