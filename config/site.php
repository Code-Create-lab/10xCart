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

    'phone' => '+919835358531',           // used in tel: links
    'phone_display' => '+91 98353 58531', // shown to visitors
    'email' => 'shreyadiwivedi@gmail.com',
    // Contact-form enquiries are emailed here (queued job: App\Jobs\SendLeadNotification)
    'lead_mail' => [
        'to' => ['shreyadiwivedi@gmail.com', 'shreya@snehal.info'],
        'cc' => ['sr.snehal369@gmail.com'],
    ],

    'address' => 'Sample Business Park, Your City – 000000, India',

    'socials' => [
        ['label' => 'Facebook', 'icon' => 'b-facebook', 'url' => '#'],
        ['label' => 'X (Twitter)', 'icon' => 'b-x', 'url' => '#'],
        ['label' => 'LinkedIn', 'icon' => 'b-linkedin', 'url' => '#'],
        ['label' => 'Pinterest', 'icon' => 'b-pinterest', 'url' => '#'],
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

    'legal' => [
        'Privacy policy' => '#',
        'Terms' => '#',
    ],

];
