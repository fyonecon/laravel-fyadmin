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
        // 系统扩展服务所依赖的的接口，不可更改
        'play/upload_base64_file',
        'play/upload_form_file',
        'play/save_base64_img',
        'play/save_url_img',
        'play/log',
        'admin/login',
        'admin/login_check'

        // 其他


    ];
}
