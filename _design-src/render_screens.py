"""Render the design sources in this folder to WebP images used by index.html.

  app-screens.html  <section class="screen" id="s-NAME">  ->  media/10xcart/screen-NAME.webp  (mobile, 390x844)
  web-screens.html  <section class="web" id="w-NAME">     ->  media/10xcart/web-NAME.webp     (desktop, 1440x900)
  admin-screens.html <section class="admin" id="a-NAME"> ->  media/10xcart/admin-NAME.webp   (admin panel, 1440x810)

Usage (from the site root):  python _design-src/render_screens.py
Requires: pip install playwright pillow  (uses the installed Google Chrome)
"""
import io
from pathlib import Path

from PIL import Image
from playwright.sync_api import sync_playwright

ROOT = Path(__file__).resolve().parent.parent
OUT = ROOT / "media" / "10xcart"
JOBS = [
    # source file, element selector, id prefix, output prefix, device scale
    ("app-screens.html", "section.screen", "s-", "screen-", 1.25),  # 390 css px -> 488 px wide
    ("web-screens.html", "section.web", "w-", "web-", 0.9),  # 1440 css px -> 1296 px wide
    ("admin-screens.html", "section.admin", "a-", "admin-", 1.0),  # 1440x810, 16:9 like the browser frames
]

with sync_playwright() as p:
    browser = p.chromium.launch(channel="chrome", headless=True)
    for src, selector, id_prefix, out_prefix, scale in JOBS:
        page = browser.new_page(viewport={"width": 1600, "height": 1200}, device_scale_factor=scale)
        page.goto((ROOT / "_design-src" / src).as_uri())
        page.wait_for_load_state("networkidle")
        page.evaluate("document.fonts.ready")
        for el in page.query_selector_all(selector):
            name = el.get_attribute("id").removeprefix(id_prefix)
            img = Image.open(io.BytesIO(el.screenshot(type="png"))).convert("RGB")
            target = OUT / f"{out_prefix}{name}.webp"
            img.save(target, "WEBP", quality=84, method=6)
            print(f"{target.name}: {img.size[0]}x{img.size[1]}, {target.stat().st_size // 1024} KB")
        page.close()
    browser.close()
