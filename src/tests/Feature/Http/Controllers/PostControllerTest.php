<?php

namespace Tests\Feature\Http\controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function TOPページで、ブログ一覧が表示されること()
    {
        // $this->withoutExceptionHandling();

        // $post1 = Post::factory()->create();
        // $post2 = Post::factory()->create();

        // $this->get('/')
        //     ->assertOk()
        //     ->assertSee($post1->title)
        //     ->assertSee($post2->title);

        $post1 = Post::factory()->hasComments(3)->create(['title' => 'ブログのタイトル１']);
        $post2 = Post::factory()->hasComments(5)->create(['title' => 'ブログのタイトル２']);
        Post::factory()->hasComments(1)->create();

        $this->get('/')
            ->assertSee('ブログのタイトル１')
            ->assertSee('ブログのタイトル２')
            ->assertSee($post1->user->name)
            ->assertSee($post2->user->name)
            ->assertSee('（3件のコメント）')
            ->assertSee('（5件のコメント）')
            ->assertSeeInOrder([
                '（5件のコメント）',
                '（3件のコメント）',
                '（1件のコメント）',
            ]);
    }

    /** @test */
    public function ブログの一覧で、非公開のブログは表示されないこと()
    {
        $post1 = Post::factory()->closed()->create([
            'title' => 'これは非公開のブログです。',
        ]);
        $post2 = Post::factory()->create([
            'title' => 'これは公開済みのブログです。',
        ]);

        $this->get('/')
            ->assertDontSee('これは非公開のブログです。')
            ->assertSee('これは公開済みのブログです。');
    }

    /** @test */
    public function ブログの詳細画面が表示されること()
    {
        $post = Post::factory()->create();

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->user->name);
    }

    /** @test */
    public function ブログの詳細画面にてコメントが古い順番で表示されていること()
    {
        $post = Post::factory()->create();

        Comment::factory()->createMany([
            ['created_at' => now()->sub('2 days'), 'name' => 'コメント太郎', 'post_id' => $post->id],
            ['created_at' => now()->sub('3 days'), 'name' => 'コメント次郎', 'post_id' => $post->id],
            ['created_at' => now()->sub('1 days'), 'name' => 'コメント三郎', 'post_id' => $post->id],
        ]);

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->user->name)
            ->assertSeeInOrder(['コメント次郎', 'コメント太郎', 'コメント三郎']);
    }

    /** @test */
    public function ブログで非公開のものは、詳細表示されないこと()
    {
        $post = Post::factory()->closed()->create();

        $this->get('posts/'.$post->id)
            ->assertForbidden();
    }

    /** @test */
    function クリスマスの日は、メリークリスマス！と表示されること()
    {
        $post = Post::factory()->create();

        Carbon::setTestNow('2020-12-24');

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertDontSee('メリークリスマス！');

        Carbon::setTestNow('2020-12-25');

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee('メリークリスマス！');
    }

}
