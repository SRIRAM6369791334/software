<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['vehicle_number', 'vehicle_type', 'capacity'];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
