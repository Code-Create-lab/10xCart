"""Render the design sources in this folder to the images used by index.html.

  app-screens.html   <section class="screen" id="s-NAME">  ->  media/10xcart/screen-NAME.webp  (mobile, 390x844)
  web-screens.html   <section class="web" id="w-NAME">     ->  media/10xcart/web-NAME.webp     (desktop, 1440x900)
  admin-screens.html <section class="admin" id="a-NAME">   ->  media/10xcart/admin-NAME.webp   (admin panel, 1440x810)
  og-image.html      <section class="og" id="og-image">    ->  assets/img/brand/og-image.jpg   (social share, 1200x630)

Usage (from the site root):  python _design-src/render_screens.py
Requires: pip install playwright pillow  (uses the installed Google Chrome)
"""
import io
from pathlib import Path

from PIL import Image
from playwright.sync_api import sync_playwright

ROOT = Path(__file__).resolve().parent.parent
MEDIA = ROOT / "media" / "10xcart"
BRAND = ROOT / "assets" / "img" / "brand"
JOBS = [
    # source file, element selector, id prefix, output dir, output prefix, format, device scale
    ("app-screens.html", "section.screen", "s-", MEDIA, "screen-", "webp", 1.25),  # 390 css px -> 488 px wide
    ("web-screens.html", "section.web", "w-", MEDIA, "web-", "webp", 0.9),  # 1440 css px -> 1296 px wide
    ("admin-screens.html", "section.admin", "a-", MEDIA, "admin-", "webp", 1.0),  # 1440x810, 16:9 like the browser frames
    ("og-image.html", "section.og", "", BRAND, "", "jpg", 1.0),  # og-image.jpg, 1200x630 (JPEG: best link-preview support)
]

with sync_playwright() as p:
    browser = p.chromium.launch(channel="chrome", headless=True)
    for src, selector, id_prefix, out_dir, out_prefix, fmt, scale in JOBS:
        page = browser.new_page(viewport={"width": 1600, "height": 1200}, device_scale_factor=scale)
        page.goto((ROOT / "_design-src" / src).as_uri())
        page.wait_for_load_state("networkidle")
        page.evaluate("document.fonts.ready")
        for el in page.query_selector_all(selector):
            name = el.get_attribute("id").removeprefix(id_prefix)
            img = Image.open(io.BytesIO(el.screenshot(type="png"))).convert("RGB")
            target = out_dir / f"{out_prefix}{name}.{fmt}"
            if fmt == "jpg":
                img.save(target, "JPEG", quality=86, optimize=True, progressive=True)
            else:
                img.save(target, "WEBP", quality=84, method=6)
            print(f"{target.name}: {img.size[0]}x{img.size[1]}, {target.stat().st_size // 1024} KB")
        page.close()
    browser.close()
