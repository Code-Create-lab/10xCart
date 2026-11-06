<?php

/*
|--------------------------------------------------------------------------
| Work page (/work): portfolio shared with clients
|--------------------------------------------------------------------------
| Not linked from the home page and hidden from search engines (noindex):
| send the link to clients directly. View: resources/views/pages/work.
|
| To add a project, copy one of the entries below. Screenshots live in
| public/media/work as "{name}.webp" (1368 px wide, 16:10) plus "{name}-640.webp"
| and "{name}-960.webp"; list the names under "images".
|
| "visual" decides the picture on the card:
|   screens  – browser screenshots from "images"
|   platform – the 10xCart admin dashboard with a phone app screen
|   flow     – a diagram of "flow" steps (for back-end and private projects)
|
| Card button: "url" (live site, opens in a new tab), otherwise "link" (a home page
| section, e.g. '#contact'), otherwise the "private" note.
*/

return [

    // Filter buttons above the projects (key => label + icon from the sprite)
    'categories' => [
        'ecommerce' => ['label' => 'E-commerce', 'icon' => 'i-cart'],
        'websites' => ['label' => 'Websites & CMS', 'icon' => 'i-globe'],
        'platforms' => ['label' => 'Platforms & APIs', 'icon' => 'i-server'],
        'ai' => ['label' => 'AI & automation', 'icon' => 'i-sparkles'],
    ],

    // Numbers under the hero
    'stats' => [
        ['value' => '50+', 'label' => 'production apps shipped for brands and enterprises'],
        ['value' => '12+ years', 'label' => 'building web platforms, stores and APIs'],
        ['value' => '10k+', 'label' => 'API requests a day served by our backends'],
        ['value' => '40%', 'label' => 'faster APIs after query and index tuning'],
    ],

    // Engineer behind the projects: the "Who builds it" card, commented out for now in
    // resources/views/pages/work/sections/process.blade.php
    'lead' => [
        'name' => 'Snehal Raj',
        'role' => 'Lead engineer · Laravel & Node.js',
        'bio' => 'Senior backend engineer with 6+ years of building production systems: e-commerce platforms, booking APIs and enterprise back-ends.',
        'links' => [
            ['label' => 'GitHub', 'icon' => 'i-github', 'url' => 'https://github.com/rajvansh-369'],
            ['label' => 'LinkedIn', 'icon' => 'b-linkedin', 'url' => 'https://www.linkedin.com/in/snehal-raj-7373bb14b/'],
            ['label' => 'Upwork profile', 'icon' => 'i-shield', 'url' => 'https://www.upwork.com/freelancers/~01b0c4e9cf9f5f231b'],
        ],
    ],

    'projects' => [

        [
            'slug' => 'zakh',
            'featured' => true,
            'category' => 'ecommerce',
            'name' => 'ZAKH',
            'title' => 'Fashion e-commerce platform',
            'client' => 'Online fashion brand for Gen Z and Millennials',
            'period' => null,
            'summary' => 'A live fashion store where shoppers browse, pay securely, track shipments and return or exchange orders. We built and maintain the backend: features, third-party integrations, performance and the live production app.',
            'highlights' => [
                '600+ products across Women and Men collections',
                'Combo offers and payment-based discounts (pay online or partial COD)',
                'Order tracking, returns and exchanges',
                'Live search, cart and customer accounts',
            ],
            'metric' => ['value' => '600+', 'label' => 'products live'],
            'stack' => ['Laravel', 'Livewire', 'MySQL', 'AWS S3', 'REST API'],
            'url' => 'https://zakh.in/',
            'visual' => 'screens',
            'images' => [
                ['name' => 'zakh-home', 'alt' => 'ZAKH home page with the Festive Edit collection banner and Women and Men category shortcuts'],
                ['name' => 'zakh-products', 'alt' => 'ZAKH "New drops in" product row with sale prices and a 2-for combo offer'],
            ],
        ],

        [
            'slug' => 'yellow-ride',
            'category' => 'platforms',
            'name' => 'Yellow Ride',
            'title' => 'Ride-booking platform',
            'client' => 'Scooty, bike, auto and cab rides in Delhi NCR',
            'period' => 'Dec 2024 – Apr 2025',
            'summary' => 'Backend for a ride-booking service: the booking API used by the apps, an operations admin and live ride updates.',
            'highlights' => [
                'User authentication and the complete booking API',
                'Filament admin panel for day-to-day operations',
                'Real-time ride status updates and push notifications',
            ],
            'metric' => ['value' => '35%', 'label' => 'faster API responses'],
            'stack' => ['Laravel', 'MySQL', 'Filament', 'REST API'],
            'url' => 'https://yellowrides.com/',
            'visual' => 'screens',
            'images' => [
                ['name' => 'yellow-ride-home', 'alt' => 'Yellow Ride home page: "Your City. Your Ride. Your Way." with Book a Ride and Become a Pilot buttons'],
            ],
        ],

        [
            'slug' => 'bodhi-bliss',
            'category' => 'ecommerce',
            'name' => 'Bodhi Bliss Soap Co.',
            'title' => 'Natural skincare store',
            'client' => 'Handmade skincare brand selling across India',
            'period' => 'Jan 2024 – Feb 2025',
            'summary' => 'Complete store backend with payments, shipping and stock automated, so the brand can focus on making soap.',
            'highlights' => [
                'Razorpay payments verified with webhooks',
                'Shiprocket integration that automates shipping',
                'Admin panel for inventory and orders',
            ],
            'metric' => null,
            'stack' => ['Laravel', 'Livewire', 'MySQL', 'Razorpay', 'Shiprocket'],
            'url' => 'https://bodhiblisssoap.com/',
            'visual' => 'screens',
            'images' => [
                ['name' => 'bodhi-bliss-home', 'alt' => 'Bodhi Bliss Soap Co. home page with a handmade soap banner and the collection list: lip balm, whipped soap, massage oil and cold pressed soaps'],
            ],
        ],

        [
            'slug' => 'ammas-spices',
            'category' => 'ecommerce',
            'name' => "Amma's Spices",
            'title' => 'Spice brand online store',
            'client' => 'Homemade Indian spice blends, shipped across India',
            'period' => null,
            'summary' => 'A custom, mobile-first store that loads fast and is easy for the owner to run every day.',
            'highlights' => [
                'Catalogue with categories, cart and wishlist',
                'Cash on Delivery checkout and an order management admin',
                'WhatsApp chat and SEO-friendly pages',
            ],
            'metric' => null,
            'stack' => ['Laravel', 'Livewire', 'PHP 8.4', 'MySQL'],
            'url' => 'https://ammasspices.com/',
            'visual' => 'screens',
            'images' => [
                ['name' => 'ammas-spices-home', 'alt' => "Amma's Spices home page: \"The Chai Lover's Corner\" banner with chai masala packs and delivery badges"],
            ],
        ],

        [
            'slug' => '10xcart',
            'category' => 'ecommerce',
            'name' => '10xCart',
            'title' => 'Website + Android & iOS apps',
            'client' => 'Our own e-commerce platform',
            'period' => null,
            'summary' => 'One admin panel runs the website and both mobile apps, for a single store or a multi-vendor marketplace.',
            'highlights' => [
                'Admin for products, orders, customers and sellers',
                'Ready-made app designs for 8 industries, from grocery to fashion',
                'Single-vendor and multi-vendor ready',
            ],
            'metric' => null,
            'stack' => ['Website', 'Android app', 'iOS app', 'Admin panel'],
            'url' => null,
            'link' => ['label' => 'Explore the platform', 'anchor' => '#solutions'],
            'visual' => 'platform',
            'images' => [],
        ],

        [
            'slug' => 'ijf',
            'category' => 'websites',
            'name' => 'International JumpBall Federation',
            'title' => 'Sports federation website',
            'client' => 'Official site of the governing body for JumpBall',
            'period' => null,
            'summary' => 'Official website with a custom admin panel, so staff change every page without touching code.',
            'highlights' => [
                'Admin-managed hero slider, founder and council profiles',
                'Official rule book as a PDF download and an online reader',
                'Multi-step athlete registration with document uploads, reviewed in the admin',
            ],
            'note' => 'Also built: the Goal Shot Ball Association of Bihar website, with news, events, player profiles, a gallery and online player registration.',
            'metric' => null,
            'stack' => ['Laravel', 'Livewire', 'MySQL', 'Custom CMS'],
            'url' => 'https://internationaljumpballfederation.com/',
            'visual' => 'screens',
            'images' => [
                ['name' => 'ijf-home', 'alt' => 'International JumpBall Federation home page: "The toss starts every story." with a Register as an athlete button'],
            ],
        ],

        [
            'slug' => 'jfi',
            'category' => 'websites',
            'name' => 'Jumpball Federation of India',
            'title' => 'Federation website & player portal',
            'client' => 'Official site of the national body for Jumpball in India',
            'period' => null,
            'summary' => 'Players register online and get a Player ID, state associations apply for recognition, and anyone can check that a certificate is genuine.',
            'highlights' => [
                'Player registration with Player IDs, player login and a player database',
                'Digital championship certificates with QR verification',
                'Championship calendar and results, news, circulars and photo and video galleries',
            ],
            'metric' => null,
            'stack' => ['Laravel', 'Bootstrap', 'jQuery'],
            'url' => 'https://jumpballfederationofindia.com/',
            'visual' => 'screens',
            'images' => [
                ['name' => 'jfi-home', 'alt' => 'Jumpball Federation of India home page: "Raising the game across India" with Register as a player and Verify a certificate buttons'],
            ],
        ],

        [
            'slug' => 'enterprise-rbac',
            'category' => 'platforms',
            'name' => 'Enterprise RBAC platform',
            'title' => 'Multi-panel back-end for a global enterprise',
            'client' => 'Multinational client (name kept confidential)',
            'period' => 'Oct 2023 – present',
            'summary' => 'Backend microservices for a multi-panel system: every panel only sees its own data, and every endpoint is protected.',
            'highlights' => [
                'Role-based access control across all panels',
                'Multi-tenant data isolation per panel',
                'Indexed MongoDB schemas and secure sessions',
            ],
            'metric' => null,
            'stack' => ['Node.js', 'Express.js', 'MongoDB', 'REST API'],
            'url' => null,
            'private' => 'Enterprise project: no public screens',
            'visual' => 'flow',
            'flow' => [
                ['icon' => 'i-users', 'label' => 'Admin, operations and partner panels'],
                ['icon' => 'i-shield', 'label' => 'Node.js API: RBAC + tenant isolation'],
                ['icon' => 'i-database', 'label' => 'MongoDB: indexed, per-panel data'],
            ],
            'images' => [],
        ],

        [
            'slug' => 'ai-calling-agent',
            'category' => 'ai',
            'name' => 'AI calling agent',
            'title' => 'Voice AI on n8n + LLM',
            'client' => 'In-house R&D',
            'period' => null,
            'summary' => 'A voice agent that holds natural phone conversations. Every call flow is an n8n workflow, so it is easy to change.',
            'highlights' => [
                'Triggers, prompts, call logic and follow-ups as n8n workflows',
                'LLM-powered conversations over real phone calls',
            ],
            'metric' => null,
            'stack' => ['n8n', 'LLM', 'Voice AI', 'ExtremeEngine API'],
            'url' => null,
            'link' => ['label' => 'Ask for a demo', 'anchor' => '#contact'],
            'visual' => 'flow',
            'flow' => [
                ['icon' => 'i-zap', 'label' => 'Trigger: new lead or scheduled call'],
                ['icon' => 'i-workflow', 'label' => 'n8n workflow: prompt and call logic'],
                ['icon' => 'i-phone', 'label' => 'AI voice call + automatic follow-up'],
            ],
            'images' => [],
        ],

        [
            'slug' => 'private-llm',
            'category' => 'ai',
            'name' => 'Private LLM',
            'title' => 'Self-hosted AI',
            'client' => 'In-house R&D',
            'period' => null,
            'summary' => 'A large language model running fully on local hardware, with no cloud AI APIs: the data never leaves the machine.',
            'highlights' => [
                'Full control over the model, prompts and behaviour',
                'Private by design: nothing is sent to a third party',
            ],
            'metric' => null,
            'stack' => ['Self-hosted LLM', 'Local inference'],
            'url' => null,
            'link' => ['label' => 'Ask for a demo', 'anchor' => '#contact'],
            'visual' => 'flow',
            'flow' => [
                ['icon' => 'i-lock', 'label' => 'Your documents and prompts'],
                ['icon' => 'i-cpu', 'label' => 'Model running on your own server'],
                ['icon' => 'i-message', 'label' => 'Answers, without any cloud API'],
            ],
            'images' => [],
        ],

    ],

];
