<?php

namespace Tests\Feature;

use Tests\TestCase;

class WorkPageTest extends TestCase
{
    public function test_work_page_lists_every_project_with_its_live_link(): void
    {
        $response = $this->get(route('work'))->assertOk();

        foreach (config('work.projects') as $project) {
            $response->assertSee($project['name']);

            if ($project['url']) {
                $response->assertSee('href="'.$project['url'].'"', false);
            }
        }

        $response->assertSee('<meta content="noindex, nofollow" name="robots"/>', false)
            ->assertSee('id="work-filters"', false);
    }

    public function test_home_page_does_not_link_to_the_work_page(): void
    {
        $this->get(route('home'))->assertOk()->assertDontSee(route('work'));
    }

    public function test_section_links_lead_back_to_the_home_page_from_the_work_page(): void
    {
        $this->get(route('work'))
            ->assertSee('href="'.route('home').'#services"', false)
            ->assertSee('href="'.route('home').'#contact"', false);

        $this->get(route('home'))->assertSee('href="#services"', false);
    }

    public function test_every_project_screenshot_exists_in_all_sizes(): void
    {
        foreach (config('work.projects') as $project) {
            foreach ($project['images'] as $image) {
                foreach (['', '-640', '-960'] as $size) {
                    $this->assertFileExists(public_path("media/work/{$image['name']}{$size}.webp"));
                }
            }
        }
    }
}
