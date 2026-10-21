<?php

namespace App\Support;

/**
 * Helpers for the static front-end files in public/.
 */
class Assets
{
    /**
     * Return a CSS file from public/ ready to inline in a <style> tag.
     *
     * Comments and extra whitespace are stripped, and relative url("../…")
     * paths are made absolute so they still resolve once inlined.
     * The result is cached in storage/framework/cache and rebuilt
     * automatically whenever the CSS file changes.
     */
    public static function inlineCss(string $path): string
    {
        $file = public_path($path);
        $cache = storage_path('framework/cache/inline-'.md5($file.filemtime($file).asset('')).'.css');

        if (is_file($cache)) {
            return file_get_contents($cache);
        }

        $css = file_get_contents($file);
        $css = preg_replace('~/\*.*?\*/~s', '', $css);
        $css = preg_replace('~\s+~', ' ', $css);
        $css = preg_replace('~\s*([{};,>])\s*~', '$1', $css);
        $css = str_replace(';}', '}', trim($css));

        $base = rtrim(asset(dirname(dirname($path))), '/');
        $css = str_replace('url("../', 'url("'.$base.'/', $css);

        @file_put_contents($cache, $css);

        return $css;
    }

    /**
     * asset() URL with a ?v= cache-busting version taken from the file's modification time.
     */
    public static function versioned(string $path): string
    {
        return asset($path).'?v='.filemtime(public_path($path));
    }
}
