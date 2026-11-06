@extends('layouts.app')

{{-- Privacy policy. Keep it in step with what the site does: cookies (config/site.php → tracking, config/session.php),
     the contact form (LeadController, Support/Leads/VisitorContext), IP location (Support/Leads/IpLocation) and the
     Google tags (partials/analytics.blade.php). Business details: config/site.php → company. --}}
@section('seo')
<title>Privacy Policy | 10xCart</title>
<meta content="How 10xCart collects, uses and protects personal data from our website, contact form and client projects, the cookies we use and your DPDP Act rights." name="description"/>
<link href="{{ route('privacy') }}" rel="canonical"/>
<meta content="#09348e" name="theme-color"/>
<meta content="website" property="og:type"/>
<meta content="10xCart" property="og:site_name"/>
<meta content="en_IN" property="og:locale"/>
<meta content="{{ route('privacy') }}" property="og:url"/>
<meta content="Privacy Policy | 10xCart" property="og:title"/>
<meta content="What personal data 10xCart collects, why, who we share it with and your rights." property="og:description"/>
<meta content="{{ asset('assets/img/brand/og-image.jpg') }}" property="og:image"/>
<meta content="1200" property="og:image:width"/>
<meta content="630" property="og:image:height"/>
<meta content="10xCart — ecommerce websites and apps for small businesses" property="og:image:alt"/>
<meta content="summary_large_image" name="twitter:card"/>
@endsection

@section('content')
@php
    $company = config('site.company');
    $email = config('site.email');
    $visitorMonths = (int) round(config('site.tracking.retention_days') / 30.44);

    // Contents list: section id => heading (each <section> below prints its own heading from here)
    $toc = [
        'who-we-are' => 'Who we are',
        'what-we-collect' => 'What we collect',
        'how-we-use-it' => 'How we use it',
        'sharing' => 'Who we share it with',
        'cookies' => 'Cookies and local storage',
        'retention' => 'How long we keep it',
        'security' => 'How we protect it',
        'your-rights' => 'Your rights',
        'outside-india' => 'Data processed outside India',
        'client-data' => 'Data in the stores and apps we build',
        'children' => 'Children',
        'other-websites' => 'Other websites',
        'changes' => 'Changes to this policy',
        'grievance-officer' => 'Grievance Officer',
    ];
@endphp
<x-legal.page :toc="$toc" title="Privacy Policy" updated="2026-11-06">
<x-slot:intro>This policy explains what personal data 10xCart collects when you visit this website, contact us or work with us, why we collect it, who we share it with and the choices you have. It is governed by Indian law, including the Digital Personal Data Protection Act, 2023 (the “DPDP Act”) and the Information Technology Act, 2000.</x-slot:intro>

<x-slot:summary title="The short version">
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>We use what you send us, through the form, a call or WhatsApp, to reply to you and work on your project.</span></li>
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>Analytics and advertising cookies stay off until you click “Accept”, and you can change your choice at any time.</span></li>
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>We never sell or rent your personal data.</span></li>
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>You can ask us to see, correct or delete your data at <a href="mailto:{{ $email }}">{{ $email }}</a>.</span></li>
</x-slot:summary>

<section id="who-we-are">
<h2>{{ $toc['who-we-are'] }}</h2>
<p>In this policy, “10xCart”, “we” and “us” mean {{ $company['legal_name'] }}, the business that runs this website. We build e-commerce websites, Android and iOS apps, SaaS products and custom software for businesses. “You” means anyone who visits this website, contacts us or works with us.</p>
<p>For the personal data we collect about you, we are the <strong>Data Fiduciary</strong> under the DPDP Act: we decide why and how it is used.</p>
</section>

<section id="what-we-collect">
<h2>{{ $toc['what-we-collect'] }}</h2>
<h3>Information you give us</h3>
<ul>
<li><strong>Contact form:</strong> your name, phone number, email address, what you are interested in, the plan you picked (if any) and your message.</li>
<li><strong>Calls, email and WhatsApp:</strong> your phone number or email address and what you tell us.</li>
<li><strong>When you become a client:</strong> your business and billing details (such as business name, address and GSTIN), the content for your store or app, and the access details you share so we can set it up, such as hosting, domain, app store or payment gateway logins.</li>
</ul>
<h3>Sent along with the contact form</h3>
<p>So we can reply with the right context, the form also sends:</p>
<ul>
<li>your IP address and the approximate location worked out from it (city, region, country and network);</li>
<li>your device type, operating system, browser, screen size, language and time zone;</li>
<li>how you found us: the website that linked to us, campaign tags (such as <code>utm_source</code>) and the kind of ad you clicked, if any;</li>
<li>the page you first landed on, how many times you have visited and which sections of the page you looked at.</li>
</ul>
<p>Your browser keeps these visit details in its local storage. They stay on your device and reach us only if you send the form.</p>
<h3>Collected automatically</h3>
<ul>
<li><strong>On every visit:</strong> our server receives your IP address, browser details and the pages you request, so it can deliver the site, block spam and keep it secure. We also set the essential cookies listed below.</li>
<li><strong>Only if you click “Accept” in the cookie banner:</strong> our own visitor analytics (a random visitor ID, the pages and sections you view, time on the page, how far you scroll, how you arrived, your device details, IP address and approximate location) and Google Analytics and Google Ads cookies.</li>
</ul>
<p>Before you choose, or if you choose “Only essential”, the Google tags on this site set no cookies. They send Google only limited signals, such as that a page was viewed, which Google uses for measurement in aggregate.</p>
<p>We don't collect payment card details on this website, and we don't ask for sensitive data such as health or biometric information.</p>
</section>

<section id="how-we-use-it">
<h2>{{ $toc['how-we-use-it'] }}</h2>
<ul>
<li>To reply to your enquiry, give you a demo and send you a quote.</li>
<li>To build, launch, maintain and support your store, apps or software.</li>
<li>To send invoices and keep accounts and tax records.</li>
<li>To understand how visitors use this site and improve it (only with your consent).</li>
<li>To measure our Google Ads and show our ads to people who have visited this site (only with your consent).</li>
<li>To keep this site and our systems secure and to prevent spam and misuse.</li>
<li>To meet our legal obligations and answer lawful requests from authorities.</li>
</ul>
<p>We use your data with your consent, or for the “legitimate uses” the DPDP Act allows: for example, when you give us your details for a specific purpose such as getting a quote, or when the law requires it. We never sell or rent your personal data.</p>
</section>

<section id="sharing">
<h2>{{ $toc['sharing'] }}</h2>
<p>We share personal data only when it is needed, and only with:</p>
<ul>
<li><strong>Our team:</strong> the people who reply to you or work on your project.</li>
<li><strong>Service providers that process data for us:</strong> our website hosting and email providers (each enquiry is emailed to our team), Google (Analytics and Ads, only with your consent), the IP location services ipapi.co and ipwho.is (we send them an IP address to find its approximate location) and WhatsApp, if you choose to message us there.</li>
<li><strong>Services used for your project, on your instructions:</strong> for example your hosting provider, Google Play, the Apple App Store and the payment gateways and shipping partners you choose.</li>
<li><strong>Authorities,</strong> when the law requires it, or to protect our rights, our clients or someone's safety.</li>
<li><strong>A new owner,</strong> if our business is merged or sold. They must keep protecting your data as this policy describes.</li>
</ul>
</section>

<section id="cookies">
<h2>{{ $toc['cookies'] }}</h2>
<p>Cookies are small files a website stores in your browser. Local storage is similar, but its contents are never sent automatically. This site uses:</p>
<p class="legal-table-hint"><svg aria-hidden="true" class="i"><use href="#i-arrow-right"></use></svg>Swipe the table sideways to see how long each cookie is kept.</p>
<div aria-label="Cookies used on this site" class="legal-table-wrap" role="region" tabindex="0">
<table class="legal-table">
<thead>
<tr><th scope="col">Name</th><th scope="col">What it does</th><th scope="col">Kept for</th><th scope="col">Type</th></tr>
</thead>
<tbody>
<tr><td><code>{{ config('session.cookie') }}</code></td><td>Keeps your visit working and secure</td><td>{{ config('session.lifetime') }} minutes</td><td>Essential</td></tr>
<tr><td><code>XSRF-TOKEN</code></td><td>Protects forms, such as the contact form, from forgery</td><td>{{ config('session.lifetime') }} minutes</td><td>Essential</td></tr>
<tr><td><code>{{ config('site.tracking.consent_cookie') }}</code></td><td>Remembers your cookie choice</td><td>180 days</td><td>Essential</td></tr>
<tr><td><code>{{ config('site.tracking.visitor_cookie') }}</code></td><td>A random ID that links your visits in our own analytics</td><td>{{ $visitorMonths }} months</td><td>Analytics, after “Accept”</td></tr>
<tr><td><code>_ga</code>, <code>_ga_*</code></td><td>Google Analytics: counts visits and shows how the site is used</td><td>2 years</td><td>Analytics, after “Accept”</td></tr>
<tr><td><code>_gcl_au</code></td><td>Google Ads: counts enquiries that came from our ads</td><td>90 days</td><td>Advertising, after “Accept”</td></tr>
<tr><td><code>10xcart.visitor</code></td><td>How you found us; sent to us only with the contact form</td><td>Until you clear your browser data</td><td>Local storage</td></tr>
</tbody>
</table>
</div>
<p>After you accept, Google may also set cookies on its own domains (such as doubleclick.net) to measure and personalise ads. To change your choice, open <a href="#cookie-consent">Cookie settings</a>, which is also at the bottom of every page. You can delete cookies and local storage in your browser settings at any time.</p>
</section>

<section id="retention">
<h2>{{ $toc['retention'] }}</h2>
<ul>
<li><strong>Enquiries:</strong> as long as we need them to reply and follow up, and while you are a client. Ask us and we will delete them sooner, unless the law requires us to keep them.</li>
<li><strong>Our visitor analytics:</strong> deleted automatically after {{ $visitorMonths }} months without a visit.</li>
<li><strong>Google Analytics data:</strong> kept for the retention period set in our Google Analytics account.</li>
<li><strong>Client, billing and tax records:</strong> for as long as Indian tax and accounting laws require.</li>
<li><strong>Access details you shared for a project:</strong> deleted when our work ends. We recommend you change those passwords after we hand over.</li>
</ul>
</section>

<section id="security">
<h2>{{ $toc['security'] }}</h2>
<ul>
<li>This site is served over HTTPS, so what you send us is encrypted on the way.</li>
<li>Our visitor ID cookie is encrypted and can't be read by scripts on the page.</li>
<li>Only authorised members of our team can see enquiries and analytics, through a password-protected admin panel.</li>
<li>We use the access details you share only for your project.</li>
</ul>
<p>No system is completely secure. If a breach affects your personal data, we will tell you and the Data Protection Board of India as the law requires.</p>
</section>

<section id="your-rights">
<h2>{{ $toc['your-rights'] }}</h2>
<p>Under the DPDP Act, you have the right to:</p>
<ul>
<li><strong>Access:</strong> get a summary of the personal data we hold about you, what we do with it and who we have shared it with.</li>
<li><strong>Correction:</strong> have inaccurate or incomplete data corrected, completed or updated.</li>
<li><strong>Erasure:</strong> have your data deleted when it is no longer needed, unless the law requires us to keep it.</li>
<li><strong>Withdraw consent</strong> at any time, as easily as you gave it. For cookies, use <a href="#cookie-consent">Cookie settings</a>. This doesn't affect what we did before you withdrew it.</li>
<li><strong>Nominate</strong> someone to use these rights for you if you die or can't act for yourself.</li>
<li><strong>Grievance redressal:</strong> complain to our Grievance Officer and, if you are not satisfied with our answer, to the Data Protection Board of India.</li>
</ul>
<p>To use any of these rights, email <a href="mailto:{{ $email }}">{{ $email }}</a>. We may ask you to confirm who you are before we act, and we reply within 30 days.</p>
</section>

<section id="outside-india">
<h2>{{ $toc['outside-india'] }}</h2>
<p>Some of our providers, such as Google and the IP location services, may process data on servers outside India. Indian law allows this except to countries the Government of India restricts, and we don't transfer personal data to those countries.</p>
</section>

<section id="client-data">
<h2>{{ $toc['client-data'] }}</h2>
<p>When we build or maintain a store, app or software for a client, that client decides how their customers' data is used. The client is the Data Fiduciary and we act as their <strong>Data Processor</strong>: we use their customers' data only to build, run, maintain and support their product, on their instructions, and we keep it confidential.</p>
<p>Each client's own privacy policy explains how they use their customers' data, so questions about it should go to that business.</p>
</section>

<section id="children">
<h2>{{ $toc['children'] }}</h2>
<p>Our website and services are meant for businesses and adults. We don't knowingly collect personal data from anyone under 18. If you think a child has sent us their details, contact us and we will delete them.</p>
</section>

<section id="other-websites">
<h2>{{ $toc['other-websites'] }}</h2>
<p>This site links to other websites, such as the live projects in our portfolio, WhatsApp and Instagram. Their own privacy policies apply when you visit them.</p>
</section>

<section id="changes">
<h2>{{ $toc['changes'] }}</h2>
<p>We update this policy when our practices or the law change, and the date at the top always shows the latest version. If a change affects how we use data you have already given us, we will let you know before it applies.</p>
</section>

<section id="grievance-officer">
<h2>{{ $toc['grievance-officer'] }}</h2>
<p>For questions, requests or complaints about your personal data, contact our Grievance Officer:</p>
<dl class="legal-details">
<div><dt>Name</dt><dd>{{ $company['grievance_officer'] ?? 'Grievance Officer, 10xCart' }}</dd></div>
<div><dt>Email</dt><dd><a href="mailto:{{ $email }}">{{ $email }}</a></dd></div>
<div><dt>Phone</dt><dd><a href="tel:{{ config('site.phone') }}">{{ config('site.phone_display') }}</a></dd></div>
</dl>
<p>We aim to resolve every complaint within 30 days of receiving it.</p>
</section>
</x-legal.page>
@endsection
