<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = ['driver_name', 'phone', 'license_number'];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
