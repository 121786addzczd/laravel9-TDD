<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PostShowLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        // dd($request->ip()); //"127.0.0.1" // app/Http/Middleware/PostShowLimit.php:20

        // ユニットテスト実行中の場合、制限を適用せずに次のミドルウェアへ進む
        if ($this->runningUnitTests()) {
            return $next($request);
        }

        if (! in_array($request->ip(), ['192.168.255.255'], true)) {
            abort(403, 'Your IP is not valid.');
        }

        return $next($request);
    }

    /**
     * ユニットテストが実行中であるかどうかを確認。
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return app()->runningInConsole() && app()->runningUnitTests();
    }
}
