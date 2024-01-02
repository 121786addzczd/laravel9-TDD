<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

use Tests\TestCase;

class SignupControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザー登録画面が表示されること()
    {
        $this->get('signup')
            ->assertOk();
    }

    /** @test */
    public function ユーザー登録できること()
    {
        // データ検証
        // DBに保存
        // ログインされてからマイページにリダイレクトされること

        $validData = [
            'name' => '太郎',
            'email' => 'taro@taro.com',
            'password' => 'hogehoge',
        ];

        $this->post('signup', $validData)
            ->assertOk();

        unset($validData['password']);

        // データベースにユーザーが保存されていることを確認
        $this->assertDatabaseHas('users', $validData);

        // パスワードがハッシュ化されていることを確認
        $user = User::firstWhere($validData);
        $this->assertTrue(Hash::check('hogehoge', $user->password));
    }
}
