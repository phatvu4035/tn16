<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Active
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = $request->user();
        if ($user && $user->active == 0 ) {
            Auth::logout();
            return redirect("/login")->with("message", [
                "title" => "Thất bại",
                "content" => "Tài khoản của bạn đã bị khóa, vui lòng liên hệ admin để biết thêm chi tiết",
                "type" => "danger"
            ])->send();
        }

        return $next($request);
    }
}
