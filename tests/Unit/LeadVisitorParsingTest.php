<?php

namespace Tests\Unit;

use App\Support\Leads\TrafficSource;
use App\Support\Leads\UserAgent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LeadVisitorParsingTest extends TestCase
{
    public static function userAgents(): array
    {
        return [
            'iPhone Safari' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1', [], 'Mobile', 'iOS 17.5', 'Safari 17.5'],
            'Windows Chrome' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', [], 'Desktop', 'Windows 10/11', 'Chrome 129'],
            'Windows 11 via hints' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36 Edg/129.0.0.0', ['platform' => 'Windows', 'platform_version' => '15.0.0'], 'Desktop', 'Windows 11', 'Edge 129'],
            'Samsung Internet' => ['Mozilla/5.0 (Linux; Android 13; SM-A536E) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/25.0 Chrome/121.0.0.0 Mobile Safari/537.36', [], 'Mobile', 'Android 13', 'Samsung Internet 25'],
            'Android tablet' => ['Mozilla/5.0 (Linux; Android 12; SM-X200) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', [], 'Tablet', 'Android 12', 'Chrome 120'],
            'iPad desktop mode' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15', ['touch' => true], 'Tablet', 'iPadOS', 'Safari 17.4'],
            'Mac Firefox' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 14.5; rv:130.0) Gecko/20100101 Firefox/130.0', [], 'Desktop', 'macOS', 'Firefox 130'],
            'Instagram app' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Instagram 341.0.0.31.105', [], 'Mobile', 'iOS 17.4', 'Instagram in-app browser'],
            'curl' => ['curl/8.4.0', [], 'Bot / automated', null, null],
        ];
    }

    #[DataProvider('userAgents')]
    public function test_user_agent_parsing(string $ua, array $hints, string $device, ?string $os, ?string $browser): void
    {
        $parsed = UserAgent::parse($ua, $hints);

        $this->assertSame([$device, $os, $browser], [$parsed['device'], $parsed['os'], $parsed['browser']]);
    }

    public static function touches(): array
    {
        return [
            'utm tags win' => [['utm_source' => 'newsletter', 'utm_medium' => 'email', 'referrer' => 'https://www.google.com/'], 'newsletter / email'],
            'google ads click' => [['click' => 'gclid', 'referrer' => 'https://www.google.com/'], 'Google Ads'],
            'facebook click id' => [['click' => 'fbclid'], 'Facebook / Instagram'],
            'google india search' => [['referrer' => 'https://www.google.co.in/'], 'Google search'],
            'facebook mobile link' => [['referrer' => 'https://m.facebook.com/'], 'Facebook'],
            'linkedin short link' => [['referrer' => 'https://lnkd.in/abc'], 'LinkedIn'],
            'chatgpt' => [['referrer' => 'https://chatgpt.com/'], 'ChatGPT'],
            'other website' => [['referrer' => 'https://www.indiamart.com/x'], 'Website: indiamart.com'],
            'direct' => [[], 'Direct'],
        ];
    }

    #[DataProvider('touches')]
    public function test_traffic_source(array $touch, string $expected): void
    {
        $this->assertSame($expected, TrafficSource::describe($touch));
    }
}
