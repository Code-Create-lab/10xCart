"""Page-speed build for index.html (PageSpeed Insights / Core Web Vitals).

Run it after editing index.html, assets/css/10xcart.css or assets/js/10xcart.js:
    python _design-src/build_perf.py
build_seo.py runs it automatically as its last step. Needs: pip install fonttools brotli pillow

It (re)writes:
  assets/fonts/nunito-sans.woff2, rubik.woff2
      one small file per font family: weights 400-700 and only the characters the page uses
      (plus common punctuation and the rupee sign), cut from the full OFL fonts in _design-src/fonts/
  index.html, between the BUILD:CSS markers
      assets/css/10xcart.css minified and inlined, so the first paint needs no extra request
      (edit 10xcart.css, never the inlined copy)
  index.html, the 10xcart.js <script> tag
      adds ?v=<content hash>, so browsers can cache the file for a year and still get every update
  media/10xcart/<name>-<width>.webp, assets/img/brand/<name>-<width>.webp
      every smaller image size a srcset in index.html asks for and that does not exist yet,
      resized from <name>.webp (or <name>@2x.webp)
"""
import hashlib
import html as htmllib
import io
import posixpath
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
INDEX = ROOT / "index.html"
CSS = ROOT / "assets/css/10xcart.css"
JS = ROOT / "assets/js/10xcart.js"
FONT_SRC = Path(__file__).resolve().parent / "fonts"

# (source variable font, output file, axis locations). Weights 400-700 are the only ones the CSS uses;
# Nunito Sans' other axes are pinned to the values Google Fonts serves by default.
FONTS = [
    ("NunitoSans[YTLC,opsz,wdth,wght].ttf", "nunito-sans.woff2", {"wght": (400, 700), "wdth": 100, "opsz": 12, "YTLC": 500}),
    ("Rubik[wght].ttf", "rubik.woff2", {"wght": (400, 700)}),
]
# Always kept, so small copy edits rarely need a rebuild: printable ASCII and common typographic marks
BASE_CHARS = "".join(chr(c) for c in range(0x20, 0x7F)) + " ©®·×–—‘’“”•…€₹™"


def page_chars(src):
    body = src[src.index("<body"):]
    text = htmllib.unescape(re.sub(r"<[^>]+>", " ", re.sub(r"(?s)<script.*?</script>|<style.*?</style>", "", body)))
    # Text the JS writes into the page (form messages, button labels) and CSS content strings
    extra = JS.read_text(encoding="utf-8") + CSS.read_text(encoding="utf-8")
    return sorted(set(text + extra + BASE_CHARS) - set("\n\r\t"))


def build_fonts(chars):
    from fontTools import subset
    from fontTools.ttLib import TTFont
    from fontTools.varLib import instancer

    for src, out, axes in FONTS:
        font = instancer.instantiateVariableFont(TTFont(FONT_SRC / src, recalcTimestamp=False), axes)
        buf = io.BytesIO()
        font.save(buf)  # reload: the subsetter needs the instanced tables fully built
        buf.seek(0)
        font = TTFont(buf, recalcTimestamp=False)
        opts = subset.Options()
        opts.flavor = "woff2"
        opts.hinting = False
        opts.layout_features = ["kern", "liga", "calt", "ccmp", "locl", "mark", "mkmk"]
        sub = subset.Subsetter(opts)
        sub.populate(unicodes=[ord(c) for c in chars])
        sub.subset(font)
        font.flavor = "woff2"
        target = ROOT / "assets/fonts" / out
        buf = io.BytesIO()
        font.save(buf)
        if not target.exists() or target.read_bytes() != buf.getvalue():
            target.write_bytes(buf.getvalue())
        print(f"font  {out}: {target.stat().st_size / 1024:.1f} KB, {len(chars)} characters")


def minify_css(css):
    # Comments and strings are matched in one left-to-right pass, so a quote inside a comment
    # (or "/*" inside a string) is handled correctly. Comments are dropped, strings kept as they are.
    parts = re.split(r"""(/\*.*?\*/|"(?:[^"\\]|\\.)*"|'(?:[^'\\]|\\.)*')""", css, flags=re.S)
    out = []
    for i, p in enumerate(parts):
        if i % 2:
            out.append("" if p.startswith("/*") else p)
            continue
        p = re.sub(r"\s+", " ", p)
        p = re.sub(r"\s*([{};,])\s*", r"\1", p)
        p = re.sub(r":\s+", ":", p)
        p = p.replace(";}", "}")
        out.append(p)
    return "".join(out).strip()


def inline_css(src):
    def fix_url(m):
        u = m.group(2)
        if re.match(r"^(?:[a-z]+:|/|#)", u):
            return m.group(0)
        return f"url({m.group(1)}{posixpath.normpath(posixpath.join('assets/css', u))}{m.group(1)})"

    css = re.sub(r"""url\((["']?)([^"')]+)\1\)""", fix_url, minify_css(CSS.read_text(encoding="utf-8")))
    pat = re.compile(r"<!-- BUILD:CSS:START -->.*?<!-- BUILD:CSS:END -->", re.S)
    assert pat.search(src), "marker BUILD:CSS missing in index.html"
    print(f"css   inlined {len(css) / 1024:.1f} KB (from {CSS.stat().st_size / 1024:.1f} KB)")
    return pat.sub(lambda _: f"<!-- BUILD:CSS:START -->\n<style>{css}</style>\n<!-- BUILD:CSS:END -->", src, count=1)


def version_js(src):
    v = hashlib.sha1(JS.read_bytes()).hexdigest()[:10]
    src, n = re.subn(r'src="assets/js/10xcart\.js(?:\?v=[0-9a-f]+)?"', f'src="assets/js/10xcart.js?v={v}"', src)
    assert n == 1, "10xcart.js <script> tag missing in index.html"
    return src


def build_images(src):
    from PIL import Image

    wanted = set()
    for srcset in re.findall(r'(?:srcset|imagesrcset)="([^"]+)"', src):
        wanted |= {c.split()[0] for c in srcset.split(",")}
    for rel in sorted(wanted):
        m = re.match(r"^(.*)-(\d+)\.webp$", rel)
        target = ROOT / rel
        if not m or target.exists():
            continue
        base, width = m.group(1), int(m.group(2))
        source = next((ROOT / f"{base}{s}.webp" for s in ("", "@2x") if (ROOT / f"{base}{s}.webp").exists()), None)
        assert source, f"no source image for {rel}"
        im = Image.open(source)
        im = im.resize((width, round(im.height * width / im.width)), Image.LANCZOS)
        if im.mode == "RGBA":
            # Logos (flat colours + transparency): a 128-colour palette saved losslessly is a third of the
            # size of lossy WebP with a sharp alpha channel, and looks the same at display size
            im = im.quantize(colors=128, method=Image.Quantize.FASTOCTREE, dither=Image.Dither.NONE).convert("RGBA")
            im.save(target, "WEBP", lossless=True, quality=100, method=6)
        else:
            im.save(target, "WEBP", quality=80, method=6)
        print(f"image {rel}: {target.stat().st_size / 1024:.1f} KB")


def build():
    src = INDEX.read_text(encoding="utf-8")
    build_fonts(page_chars(src))
    build_images(src)
    src = version_js(inline_css(src))
    INDEX.write_text(src, encoding="utf-8", newline="\n")


if __name__ == "__main__":
    build()
