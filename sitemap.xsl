<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:s="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:template match="/">
<html lang="en"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="robots" content="noindex, follow"/>
<title>XML Sitemap | 10xCart</title>
<style>
:root{--bg:#f6f7fb;--card:#fff;--text:#1d2433;--muted:#667085;--line:#e4e7ec;--brand:#5b3df5}
@media (prefers-color-scheme:dark){:root{--bg:#0f1220;--card:#171b2e;--text:#e6e8f0;--muted:#98a2b3;--line:#2a3047;--brand:#8d7bff}}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font:15px/1.6 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif}
.wrap{max-width:960px;margin:0 auto;padding:40px 16px}
h1{font-size:26px;margin:0 0 6px}p{color:var(--muted);margin:0 0 24px}a{color:var(--brand);text-decoration:none;word-break:break-all}a:hover{text-decoration:underline}
.card{background:var(--card);border:1px solid var(--line);border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse}th,td{padding:12px 16px;text-align:left;border-bottom:1px solid var(--line);vertical-align:top}
th{font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)}tr:last-child td{border-bottom:0}
.imgs{display:grid;grid-template-columns:repeat(auto-fill,minmax(72px,1fr));gap:8px;margin-top:10px}
.imgs img{width:100%;height:72px;object-fit:cover;border-radius:6px;border:1px solid var(--line);background:var(--bg)}
.num{white-space:nowrap;color:var(--muted)}
</style></head><body><div class="wrap">
<h1>10xCart XML Sitemap</h1>
<p>This sitemap tells search engines which pages and images to index.
<xsl:value-of select="count(s:urlset/s:url)"/> page(s), <xsl:value-of select="count(s:urlset/s:url/image:image)"/> image(s).
Learn more at <a href="https://www.sitemaps.org/">sitemaps.org</a>.</p>
<div class="card"><table>
<tr><th>Page</th><th>Images</th><th>Last modified</th></tr>
<xsl:for-each select="s:urlset/s:url">
<tr><td><a href="{s:loc}"><xsl:value-of select="s:loc"/></a>
<div class="imgs"><xsl:for-each select="image:image"><a href="{image:loc}" title="{image:loc}"><img src="{image:loc}" alt="" loading="lazy"/></a></xsl:for-each></div></td>
<td class="num"><xsl:value-of select="count(image:image)"/></td>
<td class="num"><xsl:value-of select="s:lastmod"/></td></tr>
</xsl:for-each>
</table></div></div></body></html>
</xsl:template>
</xsl:stylesheet>
