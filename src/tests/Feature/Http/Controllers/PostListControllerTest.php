<?php

namespace Tests\Feature\Http\controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostListControllerTest extends TestCase
{
    /** @test */
    public function TOPページで、ブログ一覧が表示される()
    {
        // $this->withoutExceptionHandling();

        // $post1 = Post::factory()->create();
        // $post2 = Post::factory()->create();

        // $this->get('/')
        //     ->assertOk()
        //     ->assertSee($post1->title)
        //     ->assertSee($post2->title);

        $post1 = Post::factory()->create(['title' => 'ブログのタイトル１']);
        $post2 = Post::factory()->create(['title' => 'ブログのタイトル２']);

        $this->get('/')
            ->assertOk()
            ->assertSee('ブログのタイトル１')
            ->assertSee('ブログのタイトル２');
    }

}
