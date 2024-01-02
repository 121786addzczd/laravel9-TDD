<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class UserLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン画面が開けること()
    {
        $this->get('mypage/login')
            ->assertOk();
    }

    /** @test */
    function ログイン時の入力チェックが動作すること()
    {
        $url = 'mypage/login';

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        app()->setlocale('testing');

        $this->post($url, ['email' => ''])->assertInvalid(['email' => 'required']);
        $this->post($url, ['email' => 'aa@bb@cc'])->assertInvalid(['email' => 'email']);
        $this->post($url, ['email' => 'aa@ああ.いい'])->assertInvalid(['email' => 'email']);
        $this->post($url, ['password' => ''])->assertInvalid(['password' => 'required']);
    }

    /** @test */
    public function ログインできること()
    {
        $user = User::factory()->create([
            'email' => 'testuser@test.com',
            'password' => Hash::make('hogehoge'),
        ]);

        $this->post('mypage/login', [
            'email' => 'testuser@test.com',
            'password' => 'hogehoge',
        ])->assertRedirect('mypage/posts');

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function パスワードが間違っている場合にログインできず、エラーメッセージが表示されること()
    {
        $url = 'mypage/login';

        $user = User::factory()->create([
            'email' => 'testuser@test.com',
            'password' => Hash::make('hogehoge'),
        ]);

        $this->from($url)->post('mypage/login', [
            'email' => 'testuser@test.com',
            'password' => 'ほげほげ',
        ])->assertRedirect($url);

        $this->get($url)
            ->assertOk()
            ->assertSee('メールアドレスかパスワードが間違っています。');

        // 以下も書き方もある assertRedirectは不要になる
        $this->from($url)->followingRedirects()->post($url, [
            'email' => 'testuser@test.com',
            'password' => 'ほげほげ',
        ])
        ->assertOK()
        ->assertSee('メールアドレスかパスワードが間違っています。')
        ->assertSee('<h1>ログイン画面</h1>', false);
    }
}
