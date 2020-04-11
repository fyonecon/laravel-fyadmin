<?php

namespace App\Http\Middleware;

use Closure;

class HttpCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { // 定义了全局的api的跨域方式及接口编码规格

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
        header('Content-Type: text/html; charset=utf-8');

        date_default_timezone_set('Asia/Shanghai'); // 设置时区


        return $next($request);
    }
}
