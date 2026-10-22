<!-- ============ Contact ============ -->
<section aria-labelledby="contact-title" class="section" id="contact">
<div class="container contact-layout">
<div class="reveal">
<span class="eyebrow">Get in touch</span>
<h2 class="h2" id="contact-title">Let's build your online store</h2>
<p class="lead">Tell us a little about your business. We'll set up a free demo and recommend a plan that fits your budget.</p>
<!-- SAMPLE contact details: replace the office address and social links (here and in the footer) with your real ones. Phone ({{ config('site.phone_display') }}) and email ({{ config('site.email') }}) are real. -->
<ul class="contact-list">
<li><a href="tel:{{ config('site.phone') }}"><span class="card-icon"><svg aria-hidden="true" class="i"><use href="#i-phone"></use></svg></span><span><small>Sales</small><strong>{{ config('site.phone_display') }}</strong></span></a></li>
<li><a href="mailto:{{ config('site.email') }}"><span class="card-icon"><svg aria-hidden="true" class="i"><use href="#i-mail"></use></svg></span><span><small>Email</small><strong>{{ config('site.email') }}</strong></span></a></li>
<li><div><span class="card-icon"><svg aria-hidden="true" class="i"><use href="#i-pin"></use></svg></span><span><small>Office</small><strong>Office 101, Sample Business Park, Your City – 000000, India</strong></span></div></li>
</ul>
<div class="socials">
<a aria-label="Facebook" href="#"><svg aria-hidden="true" class="i i-fill"><use href="#b-facebook"></use></svg></a>
<a aria-label="X (Twitter)" href="#"><svg aria-hidden="true" class="i i-fill"><use href="#b-x"></use></svg></a>
<a aria-label="LinkedIn" href="#"><svg aria-hidden="true" class="i i-fill"><use href="#b-linkedin"></use></svg></a>
<a aria-label="Pinterest" href="#"><svg aria-hidden="true" class="i i-fill"><use href="#b-pinterest"></use></svg></a>
</div>
</div>
<div class="form-card reveal" style="--d:1">
<h3>Request your free demo</h3>
<p>Fields marked <span class="req">*</span> are required.</p>
<!-- Submits to LeadController@store, which queues an email to the admins (config/site.php → lead_mail).
     On success 10xcart.js reports a Google Ads conversion (config/site.php → ads_lead_conversion). -->
<form data-conversion="{{ config('site.ads_lead_conversion') }}" data-endpoint="{{ route('lead.store') }}" data-mailto="{{ config('site.email') }}" data-phone="{{ config('site.phone_display') }}" id="lead-form" novalidate>
@csrf
<div class="form-grid">
<div class="field">
<label for="f-name">Full name <span class="req" aria-hidden="true">*</span></label>
<input autocomplete="name" id="f-name" name="name" required type="text"/>
<p class="field-error" id="f-name-error" role="alert"></p>
</div>
<div class="field">
<label for="f-phone">Phone <span class="req" aria-hidden="true">*</span></label>
<input autocomplete="tel" id="f-phone" inputmode="tel" name="phone" placeholder="+91" required type="tel"/>
<p class="field-error" id="f-phone-error" role="alert"></p>
</div>
<div class="field field-full">
<label for="f-email">Work email</label>
<input autocomplete="email" id="f-email" name="email" type="email"/>
<p class="field-error" id="f-email-error" role="alert"></p>
</div>
<div class="field">
<label for="f-interest">I'm interested in <span class="req" aria-hidden="true">*</span></label>
<select id="f-interest" name="interest" required>
<option value="">Select an option</option>
<option>Single vendor store</option>
<option>Multi-vendor marketplace</option>
<option>SaaS product development</option>
<option>Custom software / web app</option>
<option>Mobile app development</option>
<option>Not sure yet</option>
</select>
<p class="field-error" id="f-interest-error" role="alert"></p>
</div>
<div class="field">
<label for="f-plan">Plan</label>
<select id="f-plan" name="plan">
<option value="">Help me choose</option>
<option>Web Only</option>
<option>Android Only</option>
<option>Android &amp; iOS</option>
<option>All in One</option>
</select>
</div>
<div class="field field-full">
<label for="f-message">Tell us about your business</label>
<textarea id="f-message" name="message" placeholder="What do you sell, and what would you like to launch?"></textarea>
</div>
</div>
<div class="form-foot">
<button class="btn btn-accent btn-block" type="submit">Request free demo <svg aria-hidden="true" class="i i-arrow"><use href="#i-send"></use></svg></button>
<p aria-live="polite" class="form-status" id="form-status"></p>
<p class="form-note">No spam. We'll only use your details to contact you about your project.</p>
</div>
</form>
</div>
</div>
</section>
