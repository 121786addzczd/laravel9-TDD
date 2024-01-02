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
}
