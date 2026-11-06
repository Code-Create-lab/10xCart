<?php

/*
|--------------------------------------------------------------------------
| 10xCart site settings
|--------------------------------------------------------------------------
| Contact details, analytics and menus shared by the layout, header,
| footer and page sections. Change them here instead of in the views.
| (The schema.org JSON-LD in resources/views/pages/home/schema.blade.php
| holds its own copy of the phone/email for search engines.)
*/

return [

    'name' => '10xCart',

    // Times in lead emails are shown in this timezone
    'timezone' => 'Asia/Kolkata',

    // Google Analytics 4 measurement ID (empty = analytics disabled)
    'ga_id' => env('GA_MEASUREMENT_ID', 'G-17DVL8E94W'),

    // Google Ads tag ID for conversions and remarketing (empty = Ads tag disabled)
    'ads_id' => env('GOOGLE_ADS_ID', 'AW-333404538'),

    // Google Ads conversion reported when the contact form is sent (empty = not reported)
    'ads_lead_conversion' => env('GOOGLE_ADS_LEAD_CONVERSION', 'AW-333404538/8ZMECJrS598YEPqy_Z4B'),

    // First-party visitor tracking (admin panel → Visitors). Runs only after the visitor
    // clicks "Accept" in the cookie banner (resources/views/partials/cookie-consent.blade.php).
    'tracking' => [
        'consent_cookie' => '10x_consent',  // "granted" or "denied", set by the banner (readable by JavaScript)
        'visitor_cookie' => '10x_vid',      // random visitor id, set by the server (encrypted, HTTP only)
        'retention_days' => 395,            // visitors inactive this long are deleted (13 months)
    ],

    'phone' => '+919835358531',           // used in tel: links
    'phone_display' => '+91 98353 58531', // shown to visitors
    'email' => 'shreyadiwivedi@gmail.com',
    // Contact-form enquiries are emailed here (queued job: App\Jobs\SendLeadNotification)
    'lead_mail' => [
        'to' => ['shreyadiwivedi@gmail.com', 'shreya@snehal.info'],
        'cc' => ['sr.snehal369@gmail.com'],
    ],

    // Sample office address: hidden for now (commented out in the footer and the home contact section)
    'address' => 'Sample Business Park, Your City – 000000, India',

    // Social profile icons in the footer and the home contact section (icons: partials/icons.blade.php)
    'socials' => [
        ['label' => 'Instagram', 'icon' => 'b-instagram', 'url' => 'https://www.instagram.com/10xcart'],
    ],

    // Header menu: "label" on desktop, "mobile" in the mobile drawer
    'nav' => [
        ['url' => '#services', 'label' => 'Services', 'mobile' => 'Services'],
        ['url' => '#solutions', 'label' => 'E-commerce', 'mobile' => 'E-commerce platform'],
        ['url' => '#features', 'label' => 'Features', 'mobile' => 'Features'],
        ['url' => '#showcase', 'label' => 'Designs', 'mobile' => 'App designs'],
        ['url' => '#pricing', 'label' => 'Pricing', 'mobile' => 'Pricing'],
        ['url' => '#faq', 'label' => 'FAQ', 'mobile' => 'FAQ'],
    ],

    // Footer link columns: heading => [label => url]
    'footer_menus' => [
        'E-commerce' => [
            'Single vendor store' => '#solutions',
            'Multi-vendor marketplace' => '#solutions',
            'Delivery boy app' => '#solutions',
            'Vendor admin app' => '#solutions',
            'Pricing' => '#pricing',
        ],
        'Services' => [
            'E-commerce development' => '#services',
            'SaaS development' => '#services',
            'Custom software' => '#services',
            'Mobile apps' => '#services',
            'UI/UX & branding' => '#services',
        ],
        'Company' => [
            'Why 10xCart' => '#why',
            'How it works' => '#process',
            'App designs' => '#showcase',
            'FAQ' => '#faq',
            'Contact' => '#contact',
        ],
    ],

    // Footer legal links ("Privacy policy" is also linked from the cookie banner)
    'legal' => [
        'Privacy policy' => '/privacy-policy',
        'Terms' => '/terms',
    ],

    // Who runs 10xCart, as named in the privacy policy and terms (resources/views/pages/legal)
    'company' => [
        'legal_name' => '10xCart',   // registered name of the business, e.g. "10xCart Technologies Pvt. Ltd."
        'grievance_officer' => null, // person who answers privacy requests (null = shown as "Grievance Officer")
        'jurisdiction' => null,      // city whose courts hear disputes, e.g. "Patna, Bihar" (null = courts in India)
    ],

];
