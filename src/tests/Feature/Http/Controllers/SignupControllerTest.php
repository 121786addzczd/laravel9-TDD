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

        // $validData = User::factory()->raw();
        // $validData = User::factory()->validData();

        $this->post('signup', $validData)
            ->assertOk();

        unset($validData['password']);

        // データベースにユーザーが保存されていることを確認
        $this->assertDatabaseHas('users', $validData);

        // パスワードがハッシュ化されていることを確認
        $user = User::firstWhere($validData);
        $this->assertTrue(Hash::check('hogehoge', $user->password));
    }

    /** @test */
    public function 不正なデータでは登録できないこと()
    {
        $url = 'signup';
        // $this->post($url, [])
        //     ->assertRedirect();

        // 注意点
        // (1) カスタムメッセージを設定している時は、そちらが優先される
        // (2) 入力エラーが出る前に言語ファイルを読もうとしている箇所がある時は、
        //      そちらもtestingに対応させる必要あり

        app()->setLocale('testing');

        // $this->post($url, ['name' => ''])->assertSessionHasErrors(['name' => 'nameは必ず指定してください。']); // ->dumpSession() // DEBUG用
        $this->post($url, ['name' => ''])->assertInvalid(['name' => 'required']);
        $this->post($url, ['name' => str_repeat('あ', 21)])->assertInvalid(['name' => 'max']);
        $this->post($url, ['name' => str_repeat('あ', 20)])->assertValid('name');
    }
}
