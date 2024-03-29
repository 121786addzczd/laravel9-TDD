<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userリレーションを返す()
    {
        $post = Post::factory()->create();

        $this->assertInstanceOf(User::class, $post->user);
    }

    /** @test */
    public function commentsリレーションのテスト()
    {
        $post = Post::factory()->create();

        $post->comments;

        $this->assertInstanceOf(Collection::class, $post->comments);
    }

    /** @test */
    public function ブログ公開・非公開のscope()
    {
        $post1 = Post::factory()->closed()->create();
        $post2 = Post::factory()->create();

        $posts = Post::onlyOpen()->get();

        $this->assertFalse($posts->contains($post1));
        $this->assertTrue($posts->contains($post2));
    }

    /** @test */
    public function ブログが非公開の場合にisClosedメソッドはtrueを返す()
    {
        $open = Post::factory()->make();
        $closed = Post::factory()->closed()->make();

        $this->assertFalse($open->isClosed());
        $this->assertTrue($closed->isClosed());
    }
}
