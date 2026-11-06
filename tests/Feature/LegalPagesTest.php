<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public static function legalPages(): array
    {
        return [
            'privacy policy' => ['privacy'],
            'terms' => ['terms'],
        ];
    }

    #[DataProvider('legalPages')]
    public function test_every_contents_link_leads_to_a_section_on_the_page(string $routeName): void
    {
        $html = $this->get(route($routeName))->assertOk()->getContent();

        preg_match('/<nav aria-label="On this page" class="legal-toc">(.*?)<\/nav>/s', $html, $contents);
        preg_match_all('/href="#([a-z-]+)"/', $contents[1] ?? '', $anchors);

        $this->assertNotEmpty($anchors[1]);

        foreach ($anchors[1] as $id) {
            $this->assertStringContainsString('<section id="'.$id.'">', $html);
        }
    }

    #[DataProvider('legalPages')]
    public function test_structured_data_describes_the_page_and_its_breadcrumb(string $routeName): void
    {
        $html = $this->get(route($routeName))->getContent();

        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $jsonLd);
        $graph = json_decode($jsonLd[1] ?? '', true)['@graph'] ?? [];

        $this->assertSame(['WebPage', 'BreadcrumbList'], array_column($graph, '@type'));
        $this->assertSame(route($routeName), $graph[0]['url']);
    }

    public function test_page_heading_is_escaped_only_once(): void
    {
        $this->get(route('terms'))->assertSee('<h1 id="legal-title">Terms &amp; Conditions</h1>', false);
    }

    public function test_privacy_policy_names_the_cookies_the_site_sets(): void
    {
        $this->get(route('privacy'))
            ->assertSee('<code>'.config('session.cookie').'</code>', false)
            ->assertSee('<code>'.config('site.tracking.consent_cookie').'</code>', false)
            ->assertSee('<code>'.config('site.tracking.visitor_cookie').'</code>', false);
    }

    public function test_footer_links_to_both_legal_pages(): void
    {
        $this->get(route('home'))
            ->assertSee('href="'.parse_url(route('privacy'), PHP_URL_PATH).'"', false)
            ->assertSee('href="'.parse_url(route('terms'), PHP_URL_PATH).'"', false);
    }
}
