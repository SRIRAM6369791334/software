<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['route_name', 'zone', 'vehicle_id', 'driver_id', 'is_active'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function dealers()
    {
        return $this->hasMany(Dealer::class);
    }
}
