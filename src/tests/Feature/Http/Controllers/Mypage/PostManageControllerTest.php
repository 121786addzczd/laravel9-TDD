<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostManageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function ログインしていないユーザーはブログを管理できないこと()
    {
        $loginUrl = 'mypage/login';

        $this->get('mypage/posts')->assertRedirect($loginUrl);
        $this->get('mypage/posts/create')->assertRedirect($loginUrl);
        $this->post('mypage/posts/create', [])->assertRedirect($loginUrl);
    }

    /** @test */
    public function 認証している場合マイページを開くことができること()
    {
        // 認証済みの場合
        // $user = User::factory()->create();

        // $this->actingAs($user)
        //     ->get('mypage/posts')
        //     ->assertOk();

        $this->login();

        $this->get('mypage/posts')
            ->assertOk();
    }

    /** @test */
    public function マイページで自分のブログのデータのみが表示されること()
    {
        $user = $this->login();

        $other = Post::factory()->create();
        $myPost = Post::factory()->create(['user_id' => $user->id]);

        $this->get('mypage/posts')
            ->assertOk()
            ->assertDontSee($other->title)
            ->assertSee($myPost->title);
    }

    /** @test */
    public function マイページでブログ新規登録画面が開けること()
    {
        $this->login();

        $this->get('mypage/posts/create')
            ->assertOk();
    }

    /** @test */
    function マイページに新しい公開ブログを登録できること()
    {
        // $this->withoutExceptionHandling();
        [$windy, $me, $alice] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body' => '私のブログ本文',
            'status' => Post::OPEN,
        ];

        $response = $this->post('mypage/posts/create', $validData);

        $post = Post::first();

        $response->assertRedirect('mypage/posts/edit/'.$post->id);

        $this->assertDatabaseHas('posts', array_merge($validData, ['user_id' => $me->id]));
    }

    /** @test */
    function マイページ、ブログを新規登録できる、非公開の場合()
    {
        // $this->markTestIncomplete();

        [$windy, $me, $alice] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body' => '私のブログ本文',
            'status' => '',
        ];

        $this->post('mypage/posts/create', $validData);

        $this->assertDatabaseHas('posts', array_merge($validData, [
            'user_id' => $me->id,
            'status' => Post::CLOSED,
        ]));
    }

    /** @test */
    function マイページ、ブログの登録時の入力チェック()
    {
        $url = 'mypage/posts/create';

        $this->login();

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        app()->setLocale('testing');

        $this->post($url, ['title' => ''])->assertInvalid(['title' => 'required']);
        $this->post($url, ['title' => str_repeat('a', 256)])->assertInvalid(['title' => 'max']);
        $this->post($url, ['title' => str_repeat('a', 255)])->assertValid('title');
        $this->post($url, ['body' => ''])->assertInvalid(['body' => 'required']);
    }
}
