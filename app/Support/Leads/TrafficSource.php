<?php

namespace App\Support\Leads;

use Illuminate\Support\Str;

/**
 * Names where a visit came from, e.g. "Google search", "Google Ads",
 * "facebook / cpc" (UTM tags) or "Direct".
 */
final class TrafficSource
{
    /** Source name => referrer host pattern ("www.", "m.", "l." prefixes are removed first) */
    private const REFERRERS = [
        'Google search' => '/^google\.[a-z.]{2,7}$/',
        'Bing search' => '/^bing\.com$/',
        'Yahoo search' => '/(^|\.)yahoo\.com$/',
        'DuckDuckGo' => '/^duckduckgo\.com$/',
        'Facebook' => '/(^|\.)facebook\.com$|^fb\.me$/',
        'Instagram' => '/^instagram\.com$/',
        'LinkedIn' => '/^linkedin\.com$|^lnkd\.in$/',
        'X (Twitter)' => '/^t\.co$|^(x|twitter)\.com$/',
        'YouTube' => '/^youtube\.com$|^youtu\.be$/',
        'WhatsApp' => '/(^|\.)whatsapp\.com$|^wa\.me$/',
        'Pinterest' => '/^pinterest\.[a-z.]{2,7}$|^pin\.it$/',
        'ChatGPT' => '/^chatgpt\.com$|^chat\.openai\.com$/',
        'Perplexity' => '/^perplexity\.ai$/',
        'Claude' => '/^claude\.ai$/',
        'Gemini' => '/^gemini\.google\.com$/',
    ];

    /** @param  array{utm_source?: string, utm_medium?: string, click?: string, referrer?: string}  $touch */
    public static function describe(array $touch): string
    {
        if (! empty($touch['utm_source'])) {
            return $touch['utm_source'].(empty($touch['utm_medium']) ? '' : ' / '.$touch['utm_medium']);
        }

        return match ($touch['click'] ?? null) {
            'gclid', 'gbraid', 'wbraid' => 'Google Ads',
            'fbclid' => 'Facebook / Instagram',
            'msclkid' => 'Microsoft Ads',
            default => self::fromReferrer($touch['referrer'] ?? null),
        };
    }

    private static function fromReferrer(?string $referrer): string
    {
        $host = Str::lower((string) parse_url((string) $referrer, PHP_URL_HOST));

        if ($host === '') {
            return 'Direct';
        }

        $host = preg_replace('/^(www|m|l|lm)\./', '', $host);

        foreach (self::REFERRERS as $name => $pattern) {
            if (preg_match($pattern, $host)) {
                return $name;
            }
        }

        return 'Website: '.$host;
    }
}
