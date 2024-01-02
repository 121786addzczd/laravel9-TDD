<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostManageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function ログインしていないユーザーはマイページを閲覧しようとするとログイン画面にリダイレクトされること()
    {
        $loginUrl = 'mypage/login';

        $this->get('mypage/posts')->assertRedirect($loginUrl);
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
}
