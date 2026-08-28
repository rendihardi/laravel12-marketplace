<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    private const VALID_COURIERS = [
        'jne', 'sicepat', 'ide', 'sap', 'jnt', 'ninja',
        'tiki', 'lion', 'anteraja', 'pos', 'ncs', 'rex',
        'rpx', 'sentral', 'star', 'wahana',
    ];

    /**
     * Get the RajaOngkir API key from .env.
     */
    private function getApiKey(): string
    {
        return env('KEY_RAJA_ONGKIR', '');
    }

    /**
     * Get the RajaOngkir base URL from .env.
     */
    private function getBaseUrl(): string
    {
        return env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1');
    }

    /**
     * Generic helper to call RajaOngkir destination endpoints with caching.
     */
    private function fetchDestination(string $path, string $cacheKey, string $label, array $query = [])
    {
        try {
            $result = Cache::remember($cacheKey, 3600, function () use ($path, $query) {
                $response = Http::withHeaders([
                    'key' => $this->getApiKey(),
                ])->get($this->getBaseUrl().$path, $query);

                return $response->json();
            });

            $data = $result['data'] ?? [];
            if (empty($data)) {
                Cache::forget($cacheKey);
            }

            return ResponseHelper::jsonResponse(true, $label, $data, 200);
        } catch (\Exception $e) {
            Log::error("RajaOngkir {$label} failed: ".$e->getMessage());

            return ResponseHelper::jsonResponse(false, "Gagal mengambil {$label}.", null, 500);
        }
    }

    // ─── Destination Endpoints ───────────────────────────────────────────

    /**
     * Search domestic destinations.
     *
     * GET /api/shipping/destination?search=...
     */
    public function destination(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2',
        ]);

        $search = $request->input('search');

        return $this->fetchDestination(
            '/destination/domestic-destination',
            'shipping_destination_'.md5($search),
            'Data Destinasi',
            ['search' => $search]
        );
    }

    /**
     * Get all provinces.
     *
     * GET /api/shipping/province
     */
    public function province()
    {
        return $this->fetchDestination(
            '/destination/province',
            'shipping_provinces',
            'Data Provinsi'
        );
    }

    /**
     * Get cities by province.
     *
     * GET /api/shipping/city/{provinceId}
     */
    public function city(string $provinceId)
    {
        return $this->fetchDestination(
            "/destination/city/{$provinceId}",
            "shipping_cities_{$provinceId}",
            'Data Kota'
        );
    }

    /**
     * Get districts by city.
     *
     * GET /api/shipping/district/{cityId}
     */
    public function district(string $cityId)
    {
        return $this->fetchDestination(
            "/destination/district/{$cityId}",
            "shipping_districts_{$cityId}",
            'Data Kecamatan'
        );
    }

    /**
     * Get sub-districts by district.
     *
     * GET /api/shipping/sub-district/{districtId}
     */
    public function subDistrict(string $districtId)
    {
        return $this->fetchDestination(
            "/destination/sub-district/{$districtId}",
            "shipping_subdistricts_{$districtId}",
            'Data Kelurahan'
        );
    }

    // ─── Shipping Cost ───────────────────────────────────────────────────

    /**
     * Calculate domestic shipping cost.
     *
     * POST /api/shipping/domestic-cost
     */
    public function domesticCost(Request $request)
    {
        $request->validate([
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer|min:1',
            'courier' => 'nullable|string',
            'price' => 'nullable|string|in:lowest,highest',
        ]);

        $courier = $request->input('courier', implode(':', self::VALID_COURIERS));

        // Validate each courier in the colon-separated list
        $courierList = array_map('strtolower', array_map('trim', explode(':', $courier)));
        $invalidCouriers = array_diff($courierList, self::VALID_COURIERS);

        if (! empty($invalidCouriers)) {
            return ResponseHelper::jsonResponse(
                false,
                'the valid courier is '.implode(', ', self::VALID_COURIERS),
                null,
                422
            );
        }

        $courierNormalized = implode(':', $courierList);

        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $weight = $request->input('weight');
        $price = $request->input('price', 'lowest');

        $cacheKey = "shipping_cost_{$origin}_{$destination}_{$weight}_{$courierNormalized}_{$price}";

        try {
            $result = Cache::remember($cacheKey, 3600, function () use ($origin, $destination, $weight, $courierNormalized, $price) {
                $response = Http::asForm()->withHeaders([
                    'key' => $this->getApiKey(),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post($this->getBaseUrl().'/calculate/domestic-cost', [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courierNormalized,
                    'price' => $price,
                ]);

                return $response->json();
            });

            // Don't cache error responses
            if (! isset($result['data']) || ! is_array($result['data'])) {
                Cache::forget($cacheKey);
                $errorMessage = $result['meta']['message'] ?? 'Gagal menghitung ongkos kirim.';
                Log::error('RajaOngkir domestic-cost error: '.json_encode($result));

                return ResponseHelper::jsonResponse(false, $errorMessage, null, 422);
            }

            return ResponseHelper::jsonResponse(true, 'Data Ongkos Kirim', $result['data'], 200);
        } catch (\Exception $e) {
            Log::error('RajaOngkir domestic-cost failed: '.$e->getMessage());

            return ResponseHelper::jsonResponse(false, 'Gagal menghitung ongkos kirim.', null, 500);
        }
    }
}
