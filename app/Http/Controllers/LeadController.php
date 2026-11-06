<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Jobs\SendLeadNotification;
use App\Models\Lead;
use App\Models\Visitor;
use App\Support\Leads\VisitorContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    /**
     * Handle the contact form: validate, save the lead, queue the admin email, reply instantly.
     * Slow work (IP location lookup, SMTP) happens in the queued job.
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::create($request->validated() + [
            'ref' => 'L'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
            'ip' => $request->ip(),
            'context' => VisitorContext::fromRequest($request),
            'visitor_id' => $this->consentedVisitorId($request),
        ]);

        SendLeadNotification::dispatch($request->validated() + [
            'id' => $lead->id,
            'ref' => $lead->ref,
            'submitted_at' => $lead->created_at->toIso8601String(),
            'ip' => $lead->ip,
            'visitor' => $lead->context,
        ]);

        return response()->json(['message' => 'Thank you! Your request has been sent.']);
    }

    /** Links the lead to the visitor's browsing history, only when analytics cookies were accepted */
    private function consentedVisitorId(StoreLeadRequest $request): ?int
    {
        $uuid = $request->cookie(config('site.tracking.visitor_cookie'));

        if ($request->cookie(config('site.tracking.consent_cookie')) !== 'granted' || ! is_string($uuid)) {
            return null;
        }

        return Visitor::where('uuid', $uuid)->value('id');
    }
}
