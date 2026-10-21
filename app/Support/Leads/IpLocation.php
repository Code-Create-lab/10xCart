<?php

namespace App\Support\Leads;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * IP address → approximate location and network, via free web APIs
 * (config/services.php → ip_location). Providers are tried in order and
 * successful results are cached, so each IP is looked up at most once a day.
 * Never throws: on any problem the lead email simply says "unavailable".
 */
final class IpLocation
{
    /**
     * @return array{status: 'ok'|'private'|'unavailable', provider?: string, city?: string, region?: string,
     *               postal?: string, country?: string, country_code?: string, timezone?: string,
     *               latitude?: float, longitude?: float, network?: string, asn?: string}
     */
    public function lookup(?string $ip): array
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return ['status' => 'private']; // localhost, office LAN, …
        }

        $cacheKey = 'ip-location:'.$ip;

        if ($cached = rescue(fn () => Cache::get($cacheKey), null, false)) {
            return $cached;
        }

        foreach (config('services.ip_location.providers', []) as $provider) {
            if ($location = $this->fetch($provider, $ip)) {
                $location = ['status' => 'ok', 'provider' => $provider] + $location;
                rescue(fn () => Cache::put($cacheKey, $location, now()->addHours(config('services.ip_location.cache_hours', 24))), null, false);

                return $location;
            }
        }

        return ['status' => 'unavailable'];
    }

    private function fetch(string $provider, string $ip): ?array
    {
        return rescue(fn () => match ($provider) {
            'ipapi' => $this->fromIpapi($this->http()->get("https://ipapi.co/{$ip}/json/", array_filter([
                'key' => config('services.ip_location.ipapi_key'),
            ]))->json()),
            'ipwhois' => $this->fromIpwhois($this->http()->get("https://ipwho.is/{$ip}")->json()),
            default => null,
        });
    }

    private function http(): PendingRequest
    {
        return Http::timeout(config('services.ip_location.timeout', 4))
            ->acceptJson()
            ->withUserAgent(config('site.name').' lead mailer');
    }

    /** https://ipapi.co/api/ */
    private function fromIpapi(?array $d): ?array
    {
        if (! $d || ! empty($d['error']) || empty($d['country_code'])) {
            return null; // includes "RateLimited"
        }

        return $this->clean([
            'city' => $d['city'] ?? null,
            'region' => $d['region'] ?? null,
            'postal' => $d['postal'] ?? null,
            'country' => $d['country_name'] ?? null,
            'country_code' => $d['country_code'],
            'timezone' => $d['timezone'] ?? null,
            'latitude' => $d['latitude'] ?? null,
            'longitude' => $d['longitude'] ?? null,
            'network' => $d['org'] ?? null,
            'asn' => $d['asn'] ?? null,
        ]);
    }

    /** https://ipwhois.io/documentation */
    private function fromIpwhois(?array $d): ?array
    {
        if (! $d || ($d['success'] ?? false) !== true) {
            return null;
        }

        return $this->clean([
            'city' => $d['city'] ?? null,
            'region' => $d['region'] ?? null,
            'postal' => $d['postal'] ?? null,
            'country' => $d['country'] ?? null,
            'country_code' => $d['country_code'] ?? null,
            'timezone' => $d['timezone']['id'] ?? null,
            'latitude' => $d['latitude'] ?? null,
            'longitude' => $d['longitude'] ?? null,
            'network' => $d['connection']['isp'] ?? $d['connection']['org'] ?? null,
            'asn' => isset($d['connection']['asn']) ? 'AS'.$d['connection']['asn'] : null,
        ]);
    }

    /** Trim strings, keep numbers as floats, drop empty values */
    private function clean(array $data): array
    {
        $data = array_map(fn ($v) => match (true) {
            is_string($v) => Str::limit(trim($v, " \t\n\r\0\x0B,;"), 100, ''), // "Atria Pvt. Ltd.," → "Atria Pvt. Ltd."
            is_int($v) || is_float($v) => (float) $v,
            default => null,
        }, $data);

        return array_filter($data, fn ($v) => $v !== null && $v !== '');
    }
}
