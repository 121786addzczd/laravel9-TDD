<?php

namespace Tests\Feature\Http\controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostListControllerTest extends TestCase
{
    /** @test */
    public function TOPページで、ブログ一覧が表示される()
    {
        // $this->withoutExceptionHandling();

        $this->get('/')
            ->assertOk();
    }

}
