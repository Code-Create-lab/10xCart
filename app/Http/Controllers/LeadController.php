<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Jobs\SendLeadNotification;
use App\Support\Leads\VisitorContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    /**
     * Handle the contact form: validate, queue the admin email, reply instantly.
     * Slow work (IP location lookup, SMTP) happens in the queued job.
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        SendLeadNotification::dispatch($request->validated() + [
            'ref' => 'L'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
            'submitted_at' => now()->toIso8601String(),
            'ip' => $request->ip(),
            'visitor' => VisitorContext::fromRequest($request),
        ]);

        return response()->json(['message' => 'Thank you! Your request has been sent.']);
    }
}
