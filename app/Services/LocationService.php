<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Get location data from IP address.
     * 
     * @param string|null $ip
     * @return array|null
     */
    public function getLocationData(?string $ip = null)
    {
        // Use current request IP if none provided
        $ip = $ip ?: request()->ip();

        // For local development, request()ip() might return 127.0.0.1 or ::1
        // Handle local IP by using a default or empty data
        if ($ip === '127.0.0.1' || $ip === '::1') {
            Log::info("Location detection skipped for local IP: $ip");
            return null;
        }

        try {
            $response = Http::get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return [
                        'latitude' => $data['lat'],
                        'longitude' => $data['lon'],
                        'city' => $data['city'],
                        'country' => $data['country'],
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("IP Location Error: " . $e->getMessage());
        }

        return null;
    }
}
