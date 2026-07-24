<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_landing_page_is_displayed(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertViewIs('welcome')
            ->assertSee('Pilih crypto dengan')
            ->assertSee('Buat Ranking Saya')
            ->assertSee('Metode SAW');
    }
}
