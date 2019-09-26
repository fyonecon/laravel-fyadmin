<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [ // 声明跨域白名单路由、接口
        /*
         * 系统扩展服务所依赖的的接口，不可更改
         * */
        'enhance/upload_base64_file',
        'enhance/upload_form_file',
        'enhance/save_base64_img',
        'enhance/save_url_img',
        'enhance/log',
        'admin/login',
        'admin/login_check',

        /*
         * 其他接口
         * */
        // Admin端
        'admin/list_user',
        'admin/list_admin',
        // App端


    ];
}
