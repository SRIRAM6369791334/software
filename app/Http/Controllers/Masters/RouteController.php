<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RouteController extends Controller
{
    public function index(): View
    {
        $routes = Route::with(['vehicle', 'driver'])->withCount(['customers', 'dealers'])->get();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        return view('routes.index', compact('routes', 'vehicles', 'drivers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'route_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        Route::create($request->all());

        return redirect()->route('routes.index')->with('success', 'Route created successfully.');
    }

    public function update(Request $request, Route $route): RedirectResponse
    {
        $request->validate([
            'route_name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $route->update($request->all());

        return redirect()->route('routes.index')->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route): RedirectResponse
    {
        $route->delete();
        return redirect()->route('routes.index')->with('success', 'Route deleted successfully.');
    }

    // Vehicle Management
    public function storeVehicle(Request $request): RedirectResponse
    {
        $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles',
            'vehicle_type' => 'required|string',
            'capacity' => 'nullable|integer',
        ]);

        Vehicle::create($request->all());
        return back()->with('success', 'Vehicle added.');
    }

    // Driver Management
    public function storeDriver(Request $request): RedirectResponse
    {
        $request->validate([
            'driver_name' => 'required|string',
            'phone' => 'required|string',
            'license_number' => 'nullable|string',
        ]);

        Driver::create($request->all());
        return back()->with('success', 'Driver added.');
    }
}
