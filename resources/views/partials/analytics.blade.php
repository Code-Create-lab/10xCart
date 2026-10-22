@php($tagIds = array_values(array_filter([config('site.ga_id'), config('site.ads_id')])))
@if ($tagIds)
<!-- Google tag (gtag.js) for Google Analytics 4 and Google Ads. The 176 KB library loads once the page is shown: on the
     visitor's first scroll, tap, key press or mouse move, or 5 s after load. Queued commands (page_view) are sent then. -->
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

@foreach ($tagIds as $tagId)
  gtag('config', '{{ $tagId }}');
@endforeach

  (function () {
    var events = ["scroll", "pointerdown", "pointermove", "keydown", "touchstart"];
    var loaded = false;
    function load() {
      if (loaded) return;
      loaded = true;
      events.forEach(function (e) { removeEventListener(e, load); });
      var s = document.createElement("script");
      s.async = true;
      s.src = "https://www.googletagmanager.com/gtag/js?id={{ $tagIds[0] }}";
      document.head.appendChild(s);
    }
    events.forEach(function (e) { addEventListener(e, load, { once: true, passive: true }); });
    addEventListener("load", function () { setTimeout(load, 5000); });
  })();
</script>
@endif
