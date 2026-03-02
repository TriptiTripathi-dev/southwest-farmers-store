<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /**
     * Update the user's location in session and database.
     */
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Store in session for both guests and logged-in users
        session([
            'user_latitude' => $latitude,
            'user_longitude' => $longitude,
            'location_set' => true
        ]);

        // If logged in as customer, update the database
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $customer->update([
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }
}
