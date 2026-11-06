<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    public function test_unknown_urls_get_the_site_404_page_with_links_back(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('<meta content="noindex, follow" name="robots"/>', false)
            ->assertSee('href="'.route('home').'"', false)
            ->assertSee('href="'.route('home').'#pricing"', false);
    }
}
