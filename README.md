
## laravel框架

> 目前框架版本为laravel6.0.4
+ php > 7.1.3
+ Redis

## 文档

+ 文档在目录/view-admin/里面，备份也在/view-admin/目录里面
+ 或该篇框架增强的博客https://blog.csdn.net/weixin_41827162/article/details/101025556 

## laravel-fyadmin说明
> 将以前TP5.1中的控制器结构设置移植到laravel中，所以，某些拦截的写法偏向自定义。

+ 支持七牛云、图片压缩、任意格式文件上传
+ 自定义对称加密算法
+ 自定义的接口安全验证
+ 脱离模板渲染，采用前后端分离的PHP后台管理系统的渲染
+ 微信网页授权、微信网页分享
+ 获取用户精准IP+城市
+ ...

## 控制器目录说明

+ /Http/Controllers/Admin/ 后台管理系统的接口目录
+ /Http/Controllers/App/ 前台应用的接口目录
+ /Http/Controllers/Play/ 系统对外开放、系统对接的接口目录
+ /Http/Controllers/Test/ 测试专用控制器
+ ...
+ /Http/Kit/ 放自定义框架、插件的目录
+ ...
+ /Common/common.php 公用函数
+ /Http/Controller/AdminSafeCheck.php 用于后台管理系统接口的安全验证控制器继承
+ /Http/Controller/AppSafeCheck.php 用于前台应用的接口的安全验证控制器继承
+ /Http/Controller/OpenSafeCheck.php 奔放的安全验证控制器继承
+ ...
+ /storage/upload_file/ 文件上传目录，权限777
+ /view-admin/ 后台管理系统前端代码目录
+ ...

## 接口返回：
+ state接口返回的状态：
  + 1有数据，
  + 0无数据，
  + 2接口数据请求条件不足或未知错误，
  + 403拒绝访问，
  + 301app需要升级；
+ msg：解释state的数字代表的意思；
+ paging：分页【total所有数据的条数, limit每页最多数据条数, page当前第几页(offset、page)，】;
+ test_data：测试或查看返回的数据；
+ content：请求数据的内容；

## laravel项目部署(cd到composer.json同目录)
+ 检查是否安装composer
> composer --version
+ 更换composer镜像
> composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
+ 初始化composer
> composer install
+ 有报错就解决一下报错，然后再次运行composer install，一般会报错1.文件引入的错误；2.vendor依赖的错误。
+ storage目录和bootstrap目录777权限
> 无报错就能运行项目了。

## 后台账号+密码 
> test2222+test2222

## 启动Redis，安装好Redis后需要启动redis
+ 检查是否有6379端口  
> netstat -ntlp  
+ 启动服务  
> cd /root/redis-5.0.5  
> src/redis-server  
+ 测试是否运行成功  
> src/redis-cli  
> 出现“127.0.0.1:6379> ”即代表成功。  

## 升级laravel版本
> 在composer.json里面更改laravel版本  
> 然后运行composer update命令即可升级版本  

##  
## 2019-09-19  ， 2019-09-28  
##  









<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- [UserInsights](https://userinsights.com)
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)
- [User10](https://user10.com)
- [Soumettre.fr](https://soumettre.fr/)
- [CodeBrisk](https://codebrisk.com)
- [1Forge](https://1forge.com)
- [TECPRESSO](https://tecpresso.co.jp/)
- [Runtime Converter](http://runtimeconverter.com/)
- [WebL'Agence](https://weblagence.com/)
- [Invoice Ninja](https://www.invoiceninja.com)
- [iMi digital](https://www.imi-digital.de/)
- [Earthlink](https://www.earthlink.ro/)
- [Steadfast Collective](https://steadfastcollective.com/)
- [We Are The Robots Inc.](https://watr.mx/)
- [Understand.io](https://www.understand.io/)
- [Abdel Elrafa](https://abdelelrafa.com)
- [Hyper Host](https://hyper.host)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
