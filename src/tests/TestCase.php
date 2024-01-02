<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * ユーザーをログイン状態にする。
     *
     * @param User|null $user ログインするユーザー（nullの場合、新しいユーザーが作成される）
     *
     * @return User ログインしたユーザー
     */
    public function login(User $user = null)
    {
        $user ??= User::factory()->create();

        $this->actingAs($user);

        return $user;
    }
}
