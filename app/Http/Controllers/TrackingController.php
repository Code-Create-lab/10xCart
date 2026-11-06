<?php

namespace App\Http\Controllers;

use App\Support\Tracking\RecordTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackingController extends Controller
{
    /**
     * Receive a batch from the browser tracker (sent with navigator.sendBeacon).
     * Nothing is stored unless the visitor accepted analytics cookies.
     */
    public function store(Request $request, RecordTracking $recordTracking): Response
    {
        $response = response()->noContent();

        if ($request->cookie(config('site.tracking.consent_cookie')) !== 'granted') {
            return $response;
        }

        $cookie = config('site.tracking.visitor_cookie');
        $batch = json_decode($request->getContent(), true);
        $visitor = strlen($request->getContent()) <= 20000 && is_array($batch)
            ? $recordTracking->handle($request, $request->cookie($cookie), $batch)
            : null;

        if ($visitor) {
            $response->withCookie(cookie($cookie, $visitor->uuid, config('site.tracking.retention_days') * 24 * 60));
        }

        return $response;
    }
}
