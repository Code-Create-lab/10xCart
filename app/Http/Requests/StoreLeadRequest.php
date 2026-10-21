<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    /** Options of the "I'm interested in" select (pages/home/sections/contact.blade.php) */
    public const INTERESTS = [
        'Single vendor store',
        'Multi-vendor marketplace',
        'SaaS product development',
        'Custom software / web app',
        'Mobile app development',
        'Not sure yet',
    ];

    /** Options of the "Plan" select, also used by the pricing "Get a quote" buttons */
    public const PLANS = ['Web Only', 'Android Only', 'Android & iOS', 'All in One'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Form fields only. The optional "visitor" JSON (source, device, …) is
     * read and cleaned by App\Support\Leads\VisitorContext, so bad tracking
     * data can never block a real enquiry.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\-\s]{7,20}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'interest' => ['required', 'string', Rule::in(self::INTERESTS)],
            'plan' => ['nullable', 'string', Rule::in(self::PLANS)],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
