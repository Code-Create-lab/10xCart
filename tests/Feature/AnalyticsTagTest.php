<?php

namespace Tests\Feature;

use Tests\TestCase;

class AnalyticsTagTest extends TestCase
{
    public function test_homepage_sends_google_tag_data_to_analytics_and_ads(): void
    {
        config(['site.ga_id' => 'G-TEST123', 'site.ads_id' => 'AW-987654321']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee("gtag('config', 'G-TEST123');", false);
        $response->assertSee("gtag('config', 'AW-987654321');", false);
        $response->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST123', false);
    }

    public function test_google_tag_falls_back_to_ads_id_when_analytics_is_disabled(): void
    {
        config(['site.ga_id' => '', 'site.ads_id' => 'AW-987654321']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee("gtag('config', 'AW-987654321');", false);
        $response->assertSee('https://www.googletagmanager.com/gtag/js?id=AW-987654321', false);
        $response->assertDontSee("gtag('config', '');", false);
    }

    public function test_homepage_does_not_render_google_tag_when_both_ids_are_empty(): void
    {
        config(['site.ga_id' => '', 'site.ads_id' => '']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com', false);
    }
}
