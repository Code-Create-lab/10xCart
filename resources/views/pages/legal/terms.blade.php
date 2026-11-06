@extends('layouts.app')

{{-- Terms & conditions for the website and for 10xCart's services. Commercial terms follow the offer on the home page
     (pricing and FAQ sections): custom quote, one-time setup + monthly maintenance, client-owned domain and server.
     Business details: config/site.php → company. --}}
@section('seo')
<title>Terms &amp; Conditions | 10xCart</title>
<meta content="The terms for using the 10xCart website and for our e-commerce, app and custom software services: quotes, payments, ownership, maintenance, cancellations and refunds." name="description"/>
<link href="{{ route('terms') }}" rel="canonical"/>
<meta content="#09348e" name="theme-color"/>
<meta content="website" property="og:type"/>
<meta content="10xCart" property="og:site_name"/>
<meta content="en_IN" property="og:locale"/>
<meta content="{{ route('terms') }}" property="og:url"/>
<meta content="Terms &amp; Conditions | 10xCart" property="og:title"/>
<meta content="Quotes, payments, ownership, maintenance, cancellations and refunds for 10xCart's services." property="og:description"/>
<meta content="{{ asset('assets/img/brand/og-image.jpg') }}" property="og:image"/>
@endsection

@section('content')
@php
    $company = config('site.company');

    // Contents list: section id => heading (each <section> below prints its own heading from here)
    $toc = [
        'about' => 'About these terms',
        'website' => 'Using our website',
        'services' => 'Our services',
        'quotes-and-payment' => 'Quotes, prices and payment',
        'what-you-provide' => 'What you provide',
        'timelines-and-changes' => 'Timelines, changes and launch',
        'maintenance' => 'Maintenance and support',
        'ownership' => 'Ownership and licences',
        'confidentiality' => 'Confidentiality and data',
        'cancellation' => 'Cancellation and refunds',
        'liability' => 'Warranties and liability',
        'third-party-services' => 'Third-party services',
        'governing-law' => 'Governing law and disputes',
        'changes' => 'Changes to these terms',
    ];
@endphp
<x-legal.page :toc="$toc" title="Terms &amp; Conditions" updated="2026-11-06">
<x-slot:intro>These terms apply when you use this website and when you hire 10xCart to build or maintain a store, app or software. Please read them before you accept a quote from us.</x-slot:intro>

<x-slot:summary title="In short">
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>Your quote sets the scope, price and timeline of your project.</span></li>
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>10xCart plans have a one-time setup fee and a monthly maintenance fee, billed monthly.</span></li>
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>You own your domain, your server and the content and data in your store.</span></li>
<li><svg aria-hidden="true" class="i"><use href="#i-check"></use></svg><span>Custom work we create only for you is yours once it is fully paid for.</span></li>
</x-slot:summary>

<section id="about">
<h2>{{ $toc['about'] }}</h2>
<p>These terms are an agreement between you and {{ $company['legal_name'] }} (“10xCart”, “we” or “us”). By using this website or our services, you accept them. If you accept them for a business, you confirm that you are allowed to do so on its behalf.</p>
<p>Every project also has a written quote or proposal, sent by email or as a document. If your quote says something different from these terms, the quote applies to that project.</p>
</section>

<section id="website">
<h2>{{ $toc['website'] }}</h2>
<ul>
<li>Use the site lawfully. Don't try to hack it, overload it or copy it with automated tools, and don't send spam or false details through the contact form.</li>
<li>The information on the site is general. Plans, features, app designs and demos describe what we offer; they become a commitment only in a quote we send you.</li>
<li>The text, design, logo, graphics and app designs on this site belong to 10xCart. Please don't copy or reuse them without our written permission.</li>
<li>Projects we show as our work belong to their owners. Their names, logos and screenshots are shown only to describe the work we did.</li>
</ul>
</section>

<section id="services">
<h2>{{ $toc['services'] }}</h2>
<p>We build e-commerce websites and Android and iOS apps on the 10xCart platform, for single-vendor stores and multi-vendor marketplaces. We also build custom software, SaaS products and web and mobile apps, and offer UI/UX design and branding.</p>
<p>The exact scope of your project, including features, platforms, deliverables and timeline, is set out in your quote. Anything that isn't in the quote isn't included.</p>
</section>

<section id="quotes-and-payment">
<h2>{{ $toc['quotes-and-payment'] }}</h2>
<ul>
<li><strong>Quotes</strong> are valid for the period stated in them.</li>
<li><strong>10xCart plans</strong> have a one-time setup fee and a monthly maintenance fee, billed monthly, as set out in your quote.</li>
<li><strong>Custom projects</strong> are paid as set out in your quote: for example, an advance before work starts and the rest at agreed milestones.</li>
<li><strong>Taxes:</strong> prices don't include GST or other taxes unless the quote says so. We add them as the law requires.</li>
<li><strong>Late payment:</strong> please pay invoices by their due date. If a payment is overdue, we may pause work or maintenance after reminding you, and restart once it is paid.</li>
<li><strong>Third-party costs</strong> are yours and paid separately: your domain, server or hosting, app store developer accounts, paid plugins and APIs, SMS and email services, and payment gateway fees.</li>
</ul>
</section>

<section id="what-you-provide">
<h2>{{ $toc['what-you-provide'] }}</h2>
<ul>
<li><strong>Domain and server:</strong> you buy, own and renew your domain name and cloud server. We set up your store and apps on them and connect your domain.</li>
<li><strong>App store accounts:</strong> apps are published under your own Google Play and Apple developer accounts, and you pay their fees.</li>
<li><strong>Content:</strong> product details, images, logos and text that you have the right to use. You are responsible for what you sell and publish, and for following the laws that apply to your business, such as consumer protection, e-commerce and tax rules.</li>
<li><strong>Inputs on time:</strong> the feedback, approvals and access we need to keep your project moving.</li>
</ul>
</section>

<section id="timelines-and-changes">
<h2>{{ $toc['timelines-and-changes'] }}</h2>
<ul>
<li>Timelines in a quote are estimates. They depend on the agreed scope and on getting your inputs on time, so delays on your side move the dates.</li>
<li>Changes to the scope after you accept a quote may change the price and timeline. We confirm the cost in writing before we do extra work.</li>
<li>Before launch, we share the work with you for review. Once you approve it or start using it live, it counts as accepted. Bugs found later are fixed under your maintenance plan or the support period in your quote.</li>
<li>Google and Apple decide whether to approve an app and how long their review takes. We follow their guidelines and fix the issues they raise within the agreed scope, but we can't guarantee approval or timing.</li>
</ul>
</section>

<section id="maintenance">
<h2>{{ $toc['maintenance'] }}</h2>
<p>On 10xCart plans, the monthly maintenance fee covers ongoing maintenance, updates, bug fixes and support for your store and apps while your plan is active.</p>
<p>Unless your quote says otherwise, it doesn't cover new features or custom development, redesigns, adding content for you, fixing problems caused by changes made by you or other developers, or problems with third-party services such as your server, domain, payment gateway or app store accounts. We are happy to quote for these separately.</p>
<p>We work to keep your store running smoothly, but we can't promise it will always be available or free of errors, especially where it depends on your server or other providers.</p>
</section>

<section id="ownership">
<h2>{{ $toc['ownership'] }}</h2>
<ul>
<li><strong>Your content and data</strong> stay yours. You let us use them only to deliver your project.</li>
<li><strong>Custom work:</strong> code and designs we create only for your custom project become yours once you have paid for them in full.</li>
<li><strong>The 10xCart platform,</strong> its apps and app designs, and our own tools, libraries and reusable code stay ours. Unless your quote says otherwise, we give you a non-exclusive, non-transferable licence to use them to run your business while your plan is active.</li>
<li><strong>Third-party and open-source software</strong> used in your project stays under its own licence.</li>
<li><strong>Our portfolio:</strong> we may show the work we did for you (your name, logo, screenshots and a link) in our portfolio and marketing. If you'd rather we didn't, tell us and we will take it down.</li>
</ul>
</section>

<section id="confidentiality">
<h2>{{ $toc['confidentiality'] }}</h2>
<ul>
<li>We keep your business information and the access details you share confidential, and use them only for your project. Please treat our quotes, prices and other non-public material the same way.</li>
<li>When our work involves your customers' personal data, we act on your instructions as your Data Processor, as described in our <a href="{{ route('privacy') }}#client-data">Privacy Policy</a>. You are responsible for your store's own privacy policy and terms.</li>
</ul>
</section>

<section id="cancellation">
<h2>{{ $toc['cancellation'] }}</h2>
<ul>
<li><strong>Monthly plans:</strong> you can cancel by telling us in writing. The cancellation takes effect at the end of the current billing month.</li>
<li><strong>Setup fees and project payments</strong> pay for work that starts straight away (planning, setup, design and development), so they are not refundable once work has started, unless your quote says otherwise.</li>
<li><strong>Custom projects cancelled midway:</strong> you pay for the work done up to the cancellation.</li>
<li><strong>Monthly fees</strong> are not refunded for a month that has already started.</li>
<li><strong>Our right to stop:</strong> we may suspend or end our services if invoices stay unpaid after a reminder, or if our work is used for anything unlawful.</li>
<li><strong>When our services end,</strong> we hand over your data and, for custom work you have paid for, the code, and we remove our access to your systems.</li>
</ul>
</section>

<section id="liability">
<h2>{{ $toc['liability'] }}</h2>
<ul>
<li>We do our work with reasonable skill and care. Apart from that, the website and our services are provided “as is”. We don't guarantee particular business results, such as sales, search rankings or app downloads.</li>
<li>We are not liable for indirect or consequential losses, such as lost profits, revenue or data, or for losses caused by your server, third-party services or changes made by others.</li>
<li>Our total liability for any claim is limited to the fees you paid us for the service concerned in the 12 months before the claim.</li>
<li>Nothing in these terms limits any liability that can't be limited under Indian law.</li>
</ul>
</section>

<section id="third-party-services">
<h2>{{ $toc['third-party-services'] }}</h2>
<p>Your store and apps rely on services we don't control, such as your hosting provider, domain registrar, Google Play, the Apple App Store, payment gateways and shipping, SMS and email providers. Their own terms and fees apply to you, and we are not responsible for their outages, decisions or changes.</p>
<p>Links from this site to other websites are for your convenience; we are not responsible for their content.</p>
</section>

<section id="governing-law">
<h2>{{ $toc['governing-law'] }}</h2>
<p>These terms are governed by the laws of India. If a dispute arises, please email us first so we can try to settle it together.</p>
@if ($company['jurisdiction'])
<p>If we can't settle it within 30 days, it will be decided by the courts at {{ $company['jurisdiction'] }}, which will have exclusive jurisdiction.</p>
@else
<p>If we can't settle it within 30 days, it will be decided by the competent courts in India.</p>
@endif
</section>

<section id="changes">
<h2>{{ $toc['changes'] }}</h2>
<p>We may update these terms from time to time, and the date at the top always shows the latest version. Changes apply to new quotes straight away, and to running plans from the next billing month after we tell you about them.</p>
</section>
</x-legal.page>
@endsection
