"""Build every SEO / AI-discovery artefact for the 10xCart landing page from the content of index.html.

Usage (from the site root):
    python _design-src/build_seo.py                                   # placeholder domain (https://www.example.com)
    python _design-src/build_seo.py --domain https://www.yourdomain.com

Re-run after changing any page content, prices or contact details. It (re)writes:
  index.html          <head> meta block (title, description, canonical, Open Graph, X/Twitter) + JSON-LD structured data
  robots.txt          search + AI crawlers allowed, template demo pages and design sources blocked, sitemap link
  sitemap.xml         homepage + image sitemap
  llms.txt            short Markdown guide for AI assistants (llmstxt.org)
  llms-full.txt       the full page content in clean Markdown for AI assistants
  site.webmanifest    app name, colours and icons
and adds <meta name="robots" content="noindex, nofollow"> to the leftover template demo pages.
"""
import argparse
import datetime as dt
import html as htmllib
import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
INDEX = ROOT / "index.html"

# ---------------------------------------------------------------- SEO copy (edit here)
BRAND = "10xCart"
TITLE = "Ecommerce Website & App Development in India | 10xCart"
DESCRIPTION = ("10xCart builds and maintains ecommerce websites and Android & iOS apps for small businesses in India, "
               "plus SaaS and custom software. Get a free demo.")
OG_TITLE = "10xCart — Ecommerce websites & apps for small businesses"
OG_DESCRIPTION = ("Launch your online store with a website and Android & iOS apps — single-vendor or multi-vendor, "
                  "set up and maintained for you. SaaS and custom software development too.")
ORG_DESCRIPTION = ("10xCart is an ecommerce website and app development company in India. It builds and maintains online stores, "
                   "Android and iOS apps, multi-vendor marketplaces, SaaS products and custom software for small businesses.")
SLOGAN = "Ecommerce web development"
KNOWS_ABOUT = ["Ecommerce website development", "Ecommerce app development", "Multi-vendor marketplace development",
               "Android app development", "iOS app development", "SaaS product development", "Custom software development",
               "UI/UX design", "Payment gateway integration", "Online store maintenance"]
OG_IMAGE = ("assets/img/brand/og-image.jpg", 1200, 630, "10xCart — ecommerce websites and apps for small businesses, with an admin dashboard and store app preview")
LOGO = ("assets/img/brand/logo.png", 525, 456)
PUBLISHED = "2026-10-15"
THEME = "#09348e"
AI_CRAWLERS = ["GPTBot", "OAI-SearchBot", "ChatGPT-User", "ClaudeBot", "Claude-User", "Claude-SearchBot", "anthropic-ai",
               "PerplexityBot", "Perplexity-User", "Google-Extended", "Applebot-Extended", "Bingbot", "DuckAssistBot",
               "Meta-ExternalAgent", "Amazonbot", "CCBot", "cohere-ai", "MistralAI-User"]


# ---------------------------------------------------------------- helpers
def text(fragment):
    fragment = re.sub(r"<svg.*?</svg>", "", fragment, flags=re.S)
    fragment = re.sub(r"<[^>]+>", " ", fragment)
    return re.sub(r"\s+", " ", htmllib.unescape(fragment)).strip().replace(" .", ".").replace(" ,", ",")


def plain(fragment):
    """One line of text; <ul> lists become "a; b; c." so items never run together."""
    fragment = re.sub(r"<ul>(.*?)</ul>", lambda m: " " + "; ".join(text(li) for li in re.findall(r"<li>(.*?)</li>", m.group(1), re.S)) + ". ",
                      fragment, flags=re.S)
    return re.sub(r"\s+([.;:])", r"\1", text(fragment)).replace("..", ".")


def to_md(fragment):
    """Markdown: paragraphs separated by blank lines, list items as bullets."""
    fragment = re.sub(r"<li>(.*?)</li>", lambda m: "\n- " + text(m.group(1)), fragment, flags=re.S)
    fragment = re.sub(r"</p>|</ul>", "\n\n", fragment)
    fragment = re.sub(r"<[^>]+>", "", fragment)
    lines = [re.sub(r"[ \t]+", " ", htmllib.unescape(line)).strip() for line in fragment.split("\n")]
    return re.sub(r"\n{3,}", "\n\n", "\n".join(lines)).strip()


def section(src, sid):
    m = re.search(rf'<section[^>]*\bid="{sid}"[^>]*>(.*?)</section>', src, re.S)
    assert m, f"section #{sid} not found"
    return m.group(1)


def h3_p(fragment):
    return [(text(h), text(p)) for h, p in re.findall(r"<h3>(.*?)</h3>\s*<p>(.*?)</p>", fragment, re.S)]


def esc(v):
    return htmllib.escape(v, quote=True)


def replace_block(src, name, body):
    pat = re.compile(rf"<!-- SEO:{name}:START -->.*?<!-- SEO:{name}:END -->", re.S)
    assert pat.search(src), f"marker SEO:{name} missing in index.html"
    return pat.sub(lambda _: f"<!-- SEO:{name}:START -->\n{body}\n<!-- SEO:{name}:END -->", src, count=1)


# ---------------------------------------------------------------- read the page
def extract(src):
    d = {}
    hero = section(src, "top")
    d["h1"] = text(re.search(r"<h1[^>]*>(.*?)</h1>", hero, re.S).group(1))
    d["lead"] = text(re.search(r'<p class="lead[^"]*"[^>]*>(.*?)</p>', hero, re.S).group(1))
    d["stats"] = [(text(a), text(b)) for a, b in re.findall(r"<strong>(.*?)</strong><span>(.*?)</span>", hero)]
    m = re.search(r'<ul class="marquee-list">(.*?)</ul>', src, re.S)
    d["industries"] = [text(x) for x in re.findall(r'<li class="chip">(.*?)</li>', m.group(1), re.S)]
    d["services"] = [h3_p(a)[0] for a in re.findall(r"<article class=\"card[^\"]*\"[^>]*>(.*?)</article>", section(src, "services"), re.S)]
    d["platform"] = [h3_p(a)[0] for a in re.findall(r"<article class=\"card[^\"]*\"[^>]*>(.*?)</article>", section(src, "solutions"), re.S)]
    d["process"] = h3_p(section(src, "process"))
    d["why"] = h3_p(re.search(r'<ul class="why-list">(.*?)</ul>', section(src, "why"), re.S).group(1))
    d["features"] = h3_p(section(src, "features"))
    adm = section(src, "admin")
    d["admin"] = [(text(t), text(p)) for t, p in zip(
        re.findall(r'role="tab"[^>]*>(.*?)</button>', adm, re.S),
        re.findall(r'class="tab-panel"[^>]*>\s*<p>(.*?)</p>', adm, re.S))]
    d["designs"] = [(htmllib.unescape(t), htmllib.unescape(x)) for x, t in re.findall(r'data-text="([^"]*)" data-title="([^"]*)"', section(src, "showcase"))]
    pr = section(src, "pricing")
    d["pricing_lead"] = text(re.search(r'<p class="lead">(.*?)</p>', pr, re.S).group(1))
    d["pricing_note"] = text(re.search(r'<p class="pricing-note[^"]*"><span>(.*?)</span>', pr, re.S).group(1))
    plans = []
    for art in re.findall(r'<article class="plan[^"]*"[^>]*>(.*?)</article>', pr, re.S):
        inc, mv, exc = [], [], []
        for cls, body in re.findall(r'<li(?: class="([^"]*)")?>(.*?)</li>', art, re.S):
            label = text(re.sub(r'<span class="sr-only">.*?</span>|<em class="byo">.*?</em>', "", body))
            byo = "(you provide)" if 'class="byo"' in body else ""
            if cls == "no":
                exc.append(f"{label} {byo}".strip())
            elif cls == "mv-only":
                mv.append(label)
            else:
                inc.append(label)
        plans.append({"name": text(re.search(r"<h3>(.*?)</h3>", art).group(1)),
                      "desc": text(re.search(r'<p class="plan-desc">(.*?)</p>', art).group(1)),
                      "price": text(re.search(r'<p class="plan-price">(.*?)<small>', art).group(1)),
                      "billing": text(re.search(r"<small>(.*?)</small>", art).group(1)),
                      "included": inc, "multi_vendor_only": mv, "not_included": exc})
    d["plans"] = plans
    faq = []
    for q, a in re.findall(r'<details class="faq-item[^"]*"[^>]*>\s*<summary>(.*?)</summary>\s*<div class="faq-body">(.*?)</div>\s*</details>', section(src, "faq"), re.S):
        ans_html = re.sub(r"\s+", " ", a).strip()
        faq.append((text(q), plain(a), ans_html, to_md(a)))
    d["faq"] = faq
    contact = section(src, "contact")
    d["phone_tel"] = re.search(r'href="tel:([^"]+)"', contact).group(1)
    d["phone"] = text(re.search(r'href="tel:[^"]+".*?<strong>(.*?)</strong>', contact, re.S).group(1))
    d["email"] = re.search(r'href="mailto:([^"]+)"', contact).group(1)
    return d


# ---------------------------------------------------------------- builders
def meta_block(domain):
    url = f"{domain}/"
    img = f"{domain}/{OG_IMAGE[0]}"
    rows = [
        f"<title>{esc(TITLE)}</title>",
        f'<meta content="{esc(DESCRIPTION)}" name="description"/>',
        '<meta content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" name="robots"/>',
        f'<link href="{url}" rel="canonical"/>',
        f'<meta content="{BRAND}" name="author"/>',
        f'<meta content="{BRAND}" name="application-name"/>',
        f'<meta content="{BRAND}" name="apple-mobile-web-app-title"/>',
        f'<meta content="{THEME}" name="theme-color"/>',
        "<!-- Open Graph (Facebook, LinkedIn, WhatsApp) -->",
        '<meta content="website" property="og:type"/>',
        f'<meta content="{BRAND}" property="og:site_name"/>',
        '<meta content="en_IN" property="og:locale"/>',
        f'<meta content="{url}" property="og:url"/>',
        f'<meta content="{esc(OG_TITLE)}" property="og:title"/>',
        f'<meta content="{esc(OG_DESCRIPTION)}" property="og:description"/>',
        f'<meta content="{img}" property="og:image"/>',
        '<meta content="image/jpeg" property="og:image:type"/>',
        f'<meta content="{OG_IMAGE[1]}" property="og:image:width"/>',
        f'<meta content="{OG_IMAGE[2]}" property="og:image:height"/>',
        f'<meta content="{esc(OG_IMAGE[3])}" property="og:image:alt"/>',
        "<!-- X / Twitter -->",
        '<meta content="summary_large_image" name="twitter:card"/>',
        f'<meta content="{esc(OG_TITLE)}" name="twitter:title"/>',
        f'<meta content="{esc(OG_DESCRIPTION)}" name="twitter:description"/>',
        f'<meta content="{img}" name="twitter:image"/>',
        f'<meta content="{esc(OG_IMAGE[3])}" name="twitter:image:alt"/>',
        "<!-- AI assistants: Markdown summary of this site (llmstxt.org) -->",
        f'<link href="{domain}/llms.txt" rel="alternate" title="{BRAND} — summary for AI assistants" type="text/markdown"/>',
        "<!-- Search Console / Bing Webmaster verification: paste your codes and uncomment -->",
        '<!-- <meta content="GOOGLE_VERIFICATION_CODE" name="google-site-verification"/> -->',
        '<!-- <meta content="BING_VERIFICATION_CODE" name="msvalidate.01"/> -->',
    ]
    return "\n".join(rows)


def jsonld(domain, d, date):
    url = f"{domain}/"
    org_id, site_id, page_id = f"{url}#organization", f"{url}#website", f"{url}#webpage"
    india = {"@type": "Country", "name": "India"}

    def offer(name, desc, extra=None):
        svc = {"@type": "Service", "name": name, "description": desc, "provider": {"@id": org_id}, "areaServed": india}
        if extra:
            svc.update(extra)
        return {"@type": "Offer", "itemOffered": svc}

    plan_offers = []
    for p in d["plans"]:
        desc = f'{p["desc"]} Includes: {"; ".join(p["included"])}.'
        if p["not_included"]:
            desc += f' Not included: {"; ".join(p["not_included"])}.'
        plan_offers.append({"@type": "Offer", "name": f'{p["name"]} plan', "description": f'{p["billing"]}. {desc}',
                            "itemOffered": {"@type": "Service", "name": f'{p["name"]} ecommerce plan', "provider": {"@id": org_id}}})
    graph = [
        {
            "@type": "Organization", "@id": org_id, "name": BRAND, "url": url,
            "logo": {"@type": "ImageObject", "@id": f"{url}#logo", "url": f"{domain}/{LOGO[0]}", "width": LOGO[1], "height": LOGO[2], "caption": BRAND},
            "image": {"@id": f"{url}#logo"}, "slogan": SLOGAN, "description": ORG_DESCRIPTION,
            "email": d["email"], "telephone": d["phone_tel"],
            "contactPoint": [{"@type": "ContactPoint", "contactType": "sales", "telephone": d["phone_tel"], "email": d["email"],
                              "areaServed": "IN", "availableLanguage": ["English"]}],
            "areaServed": india, "knowsAbout": KNOWS_ABOUT,
            "hasOfferCatalog": {"@type": "OfferCatalog", "name": f"{BRAND} services", "itemListElement": [
                {"@type": "OfferCatalog", "name": "Development services", "itemListElement": [offer(n, x) for n, x in d["services"]]},
                {"@type": "OfferCatalog", "name": "Ecommerce platform", "itemListElement": [offer(n, x) for n, x in d["platform"]]},
                {"@type": "OfferCatalog", "name": "Ecommerce plans (custom quote)", "itemListElement": plan_offers},
            ]},
        },
        {"@type": "WebSite", "@id": site_id, "url": url, "name": BRAND, "description": DESCRIPTION, "publisher": {"@id": org_id}, "inLanguage": "en-IN"},
        {"@type": "WebPage", "@id": page_id, "url": url, "name": TITLE, "description": DESCRIPTION, "isPartOf": {"@id": site_id},
         "about": {"@id": org_id}, "primaryImageOfPage": {"@type": "ImageObject", "url": f"{domain}/{OG_IMAGE[0]}", "width": OG_IMAGE[1], "height": OG_IMAGE[2]},
         "inLanguage": "en-IN", "datePublished": PUBLISHED, "dateModified": date},
        {"@type": "FAQPage", "@id": f"{url}#faq", "isPartOf": {"@id": page_id},
         "mainEntity": [{"@type": "Question", "name": q, "acceptedAnswer": {"@type": "Answer", "text": a_html}} for q, _, a_html, _md in d["faq"]]},
    ]
    data = json.dumps({"@context": "https://schema.org", "@graph": graph}, ensure_ascii=False, indent=1)
    return '<script type="application/ld+json">\n' + data.replace("</", "<\\/") + "\n</script>"


def robots(domain, blocked):
    lines = ["# robots.txt for " + BRAND, "# Search engines and AI assistants are welcome to read and cite this site.", ""]
    # Disallow lines first, then Allow: / — correct for both longest-match (Google, RFC 9309) and first-match parsers
    rules = [f"Disallow: {b}" for b in blocked] + ["Allow: /"]
    lines += ["User-agent: *"] + rules + [""]
    lines += ["# AI search and assistant crawlers: explicitly welcome (same rules)"]
    lines += [f"User-agent: {ua}" for ua in AI_CRAWLERS] + rules + [""]
    lines += ["# Blocked paths above: design sources and leftover template demo pages (not 10xCart content).",
              "# When you replace a template page with a real page, re-run build_seo.py (or delete the old file first).", "",
              f"Sitemap: {domain}/sitemap.xml", ""]
    return "\n".join(lines)


def sitemap(domain, date):
    imgs = [OG_IMAGE[0], LOGO[0]] + sorted(str(p.relative_to(ROOT)).replace("\\", "/") for p in (ROOT / "media" / "10xcart").glob("*.webp"))
    img_xml = "".join(f"\n    <image:image><image:loc>{domain}/{i}</image:loc></image:image>" for i in imgs)
    return ('<?xml version="1.0" encoding="UTF-8"?>\n'
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">\n'
            f"  <url>\n    <loc>{domain}/</loc>\n    <lastmod>{date}</lastmod>{img_xml}\n  </url>\n</urlset>\n")


def llms(domain, d):
    u = f"{domain}/"
    out = [f"# {BRAND}", "", f"> {ORG_DESCRIPTION}", "",
           f"- Website: {u}", f"- Sales phone: {d['phone']}", f"- Email: {d['email']}",
           "- Serves: small businesses across India", "- Pricing: custom quote per plan; one-time setup cost + monthly maintenance fee (billed monthly). Clients provide their own domain name and cloud server.",
           "", "## Services"]
    out += [f"- [{n}]({u}#services): {x}" for n, x in d["services"]]
    out += ["", "## Ecommerce platform"] + [f"- [{n}]({u}#solutions): {x}" for n, x in d["platform"]]
    out += ["", "## Plans"]
    for p in d["plans"]:
        line = f"- [{p['name']}]({u}#pricing): {p['desc']} Includes: {'; '.join(p['included'])}"
        if p["multi_vendor_only"]:
            line += f". Multi-vendor plans add: {'; '.join(p['multi_vendor_only'])}"
        if p["not_included"]:
            line += f". Not included: {'; '.join(p['not_included'])}"
        out.append(line + ".")
    out += ["", "## FAQ"] + [f"- [{q}]({u}#faq): {a}" for q, a, _, _md in d["faq"]]
    out += ["", "## Optional", f"- [Full site content]({domain}/llms-full.txt): every section of the page in Markdown",
            f"- [Sitemap]({domain}/sitemap.xml)", ""]
    return "\n".join(out)


def llms_full(domain, d, date):
    u = f"{domain}/"
    o = [f"# {BRAND} — ecommerce website & app development company in India", "",
         f"> {ORG_DESCRIPTION}", "", f"Source: {u} · Last updated: {date}", "",
         "## Contact", f"- Sales phone: {d['phone']}", f"- Email: {d['email']}", f"- Free demo: {u}#contact", "",
         "## Overview", f"**{d['h1']}**", "", d["lead"], ""]
    o += [f"- **{a}** {b}" for a, b in d["stats"]]
    o += ["", "## Industries we build for", ", ".join(d["industries"]) + ".", "", "## What we build"]
    o += [f"### {n}\n{x}\n" for n, x in d["services"]]
    o += ["## Ecommerce platform"] + [f"- **{n}**: {x}" for n, x in d["platform"]]
    o += ["", "## How it works"] + [f"{i}. **{n}**: {x}" for i, (n, x) in enumerate(d["process"], 1)]
    o += ["", "## Why choose 10xCart"] + [f"- **{n}**: {x}" for n, x in d["why"]]
    o += ["", "## Features included in every store"] + [f"- **{n}**: {x}" for n, x in d["features"]]
    o += ["", "## Admin panel"] + [f"- **{n}**: {x}" for n, x in d["admin"]]
    o += ["", "## Store designs (examples)"] + [f"- **{n}**: {x}" for n, x in d["designs"]]
    o += ["", "## Plans and pricing", d["pricing_lead"], "", d["pricing_note"], ""]
    for p in d["plans"]:
        o += [f"### {p['name']}", f"{p['desc']} Price: {p['price']} ({p['billing']}).", "", "Included:"]
        o += [f"- {x}" for x in p["included"]]
        if p["multi_vendor_only"]:
            o += ["", "Added on multi-vendor plans:"] + [f"- {x}" for x in p["multi_vendor_only"]]
        if p["not_included"]:
            o += ["", "Not included:"] + [f"- {x}" for x in p["not_included"]]
        o.append("")
    o += ["## Frequently asked questions"]
    for q, _a, _h, md in d["faq"]:
        o += [f"### {q}", md, ""]
    return "\n".join(o)


def manifest():
    return json.dumps({
        "name": "10xCart — Ecommerce Website & App Development", "short_name": BRAND, "description": DESCRIPTION,
        "start_url": "./", "scope": "./", "display": "browser", "background_color": "#ffffff", "theme_color": THEME, "lang": "en-IN",
        "icons": [{"src": "assets/img/brand/favicon-192.png", "sizes": "192x192", "type": "image/png"},
                  {"src": "assets/img/brand/icon.png", "sizes": "512x512", "type": "image/png"}]}, indent=2) + "\n"


TEMPLATE_FINGERPRINT = re.compile(r"landing HTML Template|saaspik|pixelsigns|pixelomatic", re.I)


def noindex_templates():
    """Only pages that still carry the Saaspik template fingerprint are hidden; your real pages are left alone."""
    tag = '<meta content="noindex, nofollow" name="robots"/>'
    done = []
    for f in sorted(ROOT.glob("*.html")):
        if f.name == "index.html":
            continue
        s = f.read_text(encoding="utf-8", errors="ignore")
        if not TEMPLATE_FINGERPRINT.search(s):
            continue
        if 'name="robots"' not in s:
            s = re.sub(r"(<head[^>]*>)", r"\1\n" + tag, s, count=1, flags=re.I)
            f.write_text(s, encoding="utf-8", newline="\n")
        done.append(f.name)
    return done


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--domain", default="https://www.example.com", help="live site origin, e.g. https://www.yourdomain.com")
    ap.add_argument("--date", default=dt.date.today().isoformat(), help="last-modified date (YYYY-MM-DD)")
    a = ap.parse_args()
    domain = a.domain.rstrip("/")
    src = INDEX.read_text(encoding="utf-8")
    d = extract(src)
    src = replace_block(src, "META", meta_block(domain))
    src = replace_block(src, "JSONLD", jsonld(domain, d, a.date))
    INDEX.write_text(src, encoding="utf-8", newline="\n")
    templates = noindex_templates()
    blocked = ["/_design-src/", "/dependencies/"] + [f"/{n}" for n in templates]
    (ROOT / "robots.txt").write_text(robots(domain, blocked), encoding="utf-8", newline="\n")
    (ROOT / "sitemap.xml").write_text(sitemap(domain, a.date), encoding="utf-8", newline="\n")
    (ROOT / "llms.txt").write_text(llms(domain, d), encoding="utf-8", newline="\n")
    (ROOT / "llms-full.txt").write_text(llms_full(domain, d, a.date), encoding="utf-8", newline="\n")
    (ROOT / "site.webmanifest").write_text(manifest(), encoding="utf-8", newline="\n")
    print(f"domain {domain} | {len(d['services'])} services, {len(d['platform'])} platform items, {len(d['plans'])} plans, "
          f"{len(d['features'])} features, {len(d['faq'])} FAQs | noindex on {len(templates)} template pages")


if __name__ == "__main__":
    main()
