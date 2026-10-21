<?php

namespace App\Support\Leads;

/**
 * Small User-Agent parser: device type, operating system and browser.
 * Good enough for a sales email; patterns are checked top to bottom.
 */
final class UserAgent
{
    /** Regex => name ($1, $2 = captured version) */
    private const OS = [
        '/iPad.*? OS (\d+)[_.](\d+)/' => 'iPadOS $1.$2',
        '/(?:iPhone|iPod).*? OS (\d+)[_.](\d+)/' => 'iOS $1.$2',
        '/Android (\d+(?:\.\d+)?)/' => 'Android $1',
        '/Android/' => 'Android',
        '/Windows NT 10/' => 'Windows 10/11',
        '/Windows NT 6\.3/' => 'Windows 8.1',
        '/Windows NT 6\.1/' => 'Windows 7',
        '/Windows/' => 'Windows',
        '/CrOS/' => 'ChromeOS',
        '/Mac OS X/' => 'macOS',
        '/Linux/' => 'Linux',
    ];

    private const BROWSERS = [
        '/Instagram/' => 'Instagram in-app browser',
        '/FBAN|FBAV|FB_IAB/' => 'Facebook in-app browser',
        '/LinkedInApp/' => 'LinkedIn in-app browser',
        '/Edg(?:e|A|iOS)?\/(\d+)/' => 'Edge $1',
        '/OPR\/(\d+)/' => 'Opera $1',
        '/SamsungBrowser\/(\d+)/' => 'Samsung Internet $1',
        '/UCBrowser\/(\d+)/' => 'UC Browser $1',
        '/CriOS\/(\d+)/' => 'Chrome $1',
        '/FxiOS\/(\d+)/' => 'Firefox $1',
        '/Firefox\/(\d+)/' => 'Firefox $1',
        '/Chrome\/(\d+)/' => 'Chrome $1',
        '/Version\/(\d+(?:\.\d+)?).*Safari/' => 'Safari $1',
    ];

    /**
     * @param  array{touch?: bool, platform?: ?string, platform_version?: ?string, model?: ?string}  $hints  from the browser
     * @return array{device: string, os: ?string, browser: ?string, model: ?string}
     */
    public static function parse(string $ua, array $hints = []): array
    {
        // iPads in "desktop mode" send a Mac User-Agent, but Macs have no touch screen
        $iPadAsMac = ($hints['touch'] ?? false) && str_contains($ua, 'Macintosh');

        return [
            'device' => self::device($ua, $iPadAsMac),
            'os' => self::osFromHints($hints) ?? ($iPadAsMac ? 'iPadOS' : self::first(self::OS, $ua)),
            'browser' => self::first(self::BROWSERS, $ua),
            'model' => $hints['model'] ?? null,
        ];
    }

    private static function device(string $ua, bool $iPadAsMac): string
    {
        return match (true) {
            $ua === '' => 'Unknown',
            (bool) preg_match('/bot|crawl|spider|slurp|curl|wget|python|headless|lighthouse/i', $ua) => 'Bot / automated',
            $iPadAsMac,
            (bool) preg_match('/iPad|Tablet|PlayBook|Silk|Kindle/i', $ua),
            str_contains($ua, 'Android') && ! str_contains($ua, 'Mobile') => 'Tablet',
            (bool) preg_match('/Mobi|iPhone|iPod|Android|Windows Phone/i', $ua) => 'Mobile',
            default => 'Desktop',
        };
    }

    /** Chromium client hints give the real Android/Windows version (the User-Agent string is frozen) */
    private static function osFromHints(array $hints): ?string
    {
        $major = (int) ($hints['platform_version'] ?? 0); // "14.0.0" → 14

        return match ($hints['platform'] ?? null) {
            'Android' => $major > 0 ? "Android {$major}" : null,
            'Windows' => $major >= 13 ? 'Windows 11' : ($major > 0 ? 'Windows 10' : null),
            default => null,
        };
    }

    private static function first(array $patterns, string $ua): ?string
    {
        foreach ($patterns as $regex => $name) {
            if (preg_match($regex, $ua, $m)) {
                return trim(preg_replace_callback('/\$(\d)/', fn ($x) => $m[$x[1]] ?? '', $name));
            }
        }

        return null;
    }
}
