<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
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
        $this->post('mypage/posts/edit/1', [])->assertRedirect($loginUrl);
        $this->delete('mypage/posts/delete/1')->assertRedirect($loginUrl);
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

    /** @test */
    function 自分のブログの編集画面は開けること()
    {
        $post = Post::factory()->create();

        $this->login($post->user);

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertOk();
    }

    /** @test */
    function 自分以外のブログの編集画面は開けないこと()
    {
        $post = Post::factory()->create();

        $this->login();

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertForbidden();
    }

    /** @test */
    function 自分のブログは更新できること()
    {
        $validData = [
            'title' => '新タイトル',
            'body' => '新本文',
            'status' => Post::CLOSED,
        ];

        $post = Post::factory()->create();

        $this->login($post->user);

        $this->post('mypage/posts/edit/'.$post->id, $validData)
            ->assertRedirect('mypage/posts/edit/'.$post->id);

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertSee('ブログを更新しました');

        // DBに登録されている事を確認
        $this->assertDatabaseHas('posts', $validData);

        // 新規で追加されたかも知れないため個数の確認
        $this->assertCount(1, Post::all());
        $this->assertSame(1, Post::count());

        // キャッシュをクリアして最新のデータを取得（項目が少ない場合）
        $this->assertSame('新タイトル', $post->fresh()->title);
        $this->assertSame('新本文', $post->fresh()->body);

        // キャッシュをクリアして最新のデータを取得（項目が多い場合）
        $post->refresh();
        $this->assertSame('新本文', $post->body);
        $this->assertSame('新本文', $post->body);
        $this->assertSame('新本文', $post->body);
        // 他の項目も同様に繰り返し確認
    }

    /** @test */
    function 自分以外のブログは更新できないこと()
    {
        $validData = [
            'title' => '新タイトル',
            'body' => '新本文',
            'status' => Post::CLOSED,
        ];

        $post = Post::factory()->create(['title' => '元のブログタイトル']);

        $this->login();

        $this->post('mypage/posts/edit/'.$post->id, $validData)
            ->assertForbidden();

        $this->assertSame('元のブログタイトル', $post->fresh()->title);
    }

    /** @test */
    function 自分のブログは削除はできる、かつ不随するコメントも削除されること()
    {
        $post = Post::factory()->create();

        $myPostComment = Comment::factory()->create(['post_id' => $post->id]);
        $otherPostComment = Comment::factory()->create();

        $this->login($post->user);

        $this->delete('mypage/posts/delete/'.$post->id)
            ->assertRedirect('mypage/posts');

        // ブログの削除の確認
        $this->assertModelMissing($post);
        // コメントの削除の確認
        $this->assertModelMissing($myPostComment);
        $this->assertModelExists($otherPostComment);
    }

    /** @test */
    function 自分以外のブログは削除はできないこと()
    {
        $post = Post::factory()->create();

        $this->login();

        $this->delete('mypage/posts/delete/'.$post->id)
            ->assertForbidden();

        $this->assertModelExists($post);
    }
}
