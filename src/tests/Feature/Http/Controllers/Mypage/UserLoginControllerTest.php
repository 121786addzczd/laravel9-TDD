<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
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

    /** @test */
    function 認証エラーの場合validationExceptionの例外が発生すること()
    {
        $this->withoutExceptionHandling();

        // $this->expectException(ValidationException::class);

        try {
            $this->post('mypage/login', [])
                ->assertRedirect();
            $this->fail('例外が発生しませんでしたよ。');
        } catch (ValidationException $e) {
            $this->assertSame('emailは必ず指定してください。',
                $e->errors()['email'][0]
            );
        }
    }

    /** @test */
    function 認証OKの場合validationExceptionの例外が発生しないこと()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'email' => 'aaa@bbb.net',
            'password' => Hash::make('abcd1234'),
        ]);

        try {
            $this->post('mypage/login', [
                'email' => 'aaa@bbb.net',
                'password' => 'abcd1234',
            ])->assertRedirect();
        } catch (ValidationException $e) {
            $this->fail('例外が発生してしまいましたよ。');
        }
    }

    /** @test */
    public function ログアウトできること()
    {
        $this->login();

        $this->post('mypage/logout')
            ->assertRedirect('mypage/login');

        $this->get('mypage/login')
            ->assertSee('ログアウトしました。');

        // 未認証状態の確認
        $this->assertGuest();
    }
}
