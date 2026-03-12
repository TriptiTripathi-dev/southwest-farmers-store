<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LocationService;
use App\Models\StoreDetail;
use Symfony\Component\HttpFoundation\Response;

class SetStoreMiddleware
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't run middleware for admin routes or API routes if needed
        if ($request->is('admin*') || $request->is('store*') || $request->is('api*')) {
            return $next($request);
        }

        // Only detect location if not already set or specifically requested
        if (!session()->has('store_id')) {
            $latitude = session('user_latitude');
            $longitude = session('user_longitude');

            // If not in session, try to detect via IP
            if (!$latitude || !$longitude) {
                $locationData = $this->locationService->getLocationData($request->ip());
                if ($locationData) {
                    $latitude = $locationData['latitude'];
                    $longitude = $locationData['longitude'];
                    
                    session([
                        'user_latitude' => $latitude,
                        'user_longitude' => $longitude,
                        'detected_city' => $locationData['city'],
                    ]);
                }
            }

            // Find nearest store if we have coordinates
            if ($latitude && $longitude) {
                $nearestStore = StoreDetail::where('is_active', true)
                    ->withinDistance($latitude, $longitude, 50) // Search within 50km
                    ->first();

                if ($nearestStore) {
                    session(['store_id' => $nearestStore->id]);
                } else {
                    // Fallback to first store or skip
                    $defaultStore = StoreDetail::where('is_active', true)->first();
                    if ($defaultStore) {
                        session(['store_id' => $defaultStore->id]);
                    }
                }
            } else {
                // Total fallback if no location detected
                $defaultStore = StoreDetail::where('is_active', true)->first();
                if ($defaultStore) {
                    session(['store_id' => $defaultStore->id]);
                }
            }
        }

        return $next($request);
    }
}
