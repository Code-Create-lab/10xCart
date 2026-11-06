{{-- Cookie banner. "Accept" turns on Google Analytics/Ads cookies and the first-party visitor tracker
     (admin panel → Visitors); "Only essential" keeps both off. The choice is stored in the
     config('site.tracking.consent_cookie') cookie for 180 days; "Cookie settings" in the footer reopens it.
     Behaviour: public/assets/js/10xcart.js → "Cookie consent" and "Visitor tracking". --}}
<div class="cookie-consent" id="cookie-consent" role="region" aria-label="Cookie consent" hidden
     data-cookie="{{ config('site.tracking.consent_cookie') }}" data-track="{{ route('track') }}">
<p>We use cookies to understand how visitors use this site (pages viewed, approximate location from your IP address, device and where you came from) and to measure our ads. This helps us improve 10xCart. You can change your choice any time with “Cookie settings” at the bottom of the page.@if (config('site.legal.Privacy policy') && config('site.legal.Privacy policy') !== '#') <a href="{{ config('site.legal.Privacy policy') }}">Privacy policy</a>@endif</p>
<div class="cookie-consent-actions">
<button class="btn btn-ghost" type="button" data-consent="denied">Only essential</button>
<button class="btn btn-primary" type="button" data-consent="granted">Accept</button>
</div>
</div>
