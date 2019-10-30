
## laravel框架功能增强

> 目前框架版本为laravel6.0.4
+ php > 7.1.3，推荐最新PHP版本
+ Redis需开启
+ exec()命令行函数需开启
+ 适用于负载均衡服务集群

## 联系我
+ 微信Wechat：fy66881159  
+ 博客Blog：https://blog.csdn.net/weixin_41827162  
+ GitHub：https://github.com/fyonecon  

## 文档
+ 文档在目录/根目录/文档与说明/里面，备份也在/根目录/文档与说明/目录里面
+ 或该篇框架增强的博客https://blog.csdn.net/weixin_41827162/article/details/101025556 
+ Vendor库：https://github.com/fyonecon/laravel-vendor  
+ Code库：https://github.com/fyonecon/laravel-fyadmin

## 开发模式
> Controller-Kit-SafeCheck
+ 抛弃了MVC，理念意在代码功能对应接口，前、后端分离对应接口。这样就便多人升级功能维护功能开发功能，方便调试接口
+ 服务于数据库、Api安全、前后端分离、负载均衡、统一日志记录、高速文件+高速数据
+ 带有部分反爬虫功能
+ 请求生命周期：route--middleware--SafeCheck验证(--返回json)--Controller处理数据--返回json

## laravel-fyadmin说明
> 将以前TP5.1中的控制器结构设置移植到laravel中，所以，某些拦截的写法偏向自定义，最终实现：请求Api化+扩展模块化+分布积木化。在造轮子中不断吸收优秀的思想基因，并抛弃不思进取的思维尘埃。继承旧秩序，创造新秩序。

+ 支持七牛云、图片压缩、任意格式文件上传
+ 自定义对称加密算法
+ 自定义的接口安全验证
+ 脱离模板渲染，采用前后端分离的PHP后台管理系统的渲染
+ 微信网页授权、微信网页分享
+ 获取用户精准IP+城市
+ 生成二维码、读取二维码、合成海报图
+ ...

## 控制器目录说明

+ /Http/Controllers/Admin/ 后台管理系统的接口目录
+ /Http/Controllers/App/ 前台应用的接口目录
+ /Http/Controllers/Enhance/ 系统对外开放、系统对接的接口目录，不能被继承
+ /Http/Controllers/Test/ 测试专用控制器
+ ...
+ /Http/Kit/ 放自定义框架、插件的目录
+ ...
+ /Common/Common.php 公用函数，可以直接调用
+ /Common/TraitCommon.php 需要使用use来调用，来使用公共函数，主要解决多继承问题
+ /Http/Controller/AdminSafeCheck.php 用于后台管理系统接口的安全验证控制器继承
+ /Http/Controller/AppSafeCheck.php 用于前台应用的接口的安全验证控制器继承
+ /Http/Controller/OpenSafeCheck.php 奔放的安全验证控制器继承
+ ...
+ /storage/*目录*/ 各种文件上传目录，权限777，可以运行'chmod -R 777 storage'一次性777
+ /view-admin/ 后台管理系统前端代码目录，详细使用参见/view-admin/readme.md文件
+ ...

## 接口返回规范：
+ state接口返回的状态：
  + 0无数据，
  + 1有数据，
  + 2接口数据请求条件不足或未知错误，
  + 403拒绝访问，
  + 302需要重新授权并登录（可能是user_token过期、反爬虫机制报警、防破解机制报警等）  
  + 301app需要升级；
+ msg：解释state的数字代表的意思；
+ paging：分页【total所有数据的条数, limit每页最多数据条数, page当前第几页(offset、page)，】;
+ test_data：测试或查看返回的数据；
+ content：请求数据的内容；
+ 其他说明：接口不一定全部编码都写上，接口可分为a)登录专用<1,2,403,302>;b)获取信息专用<0,1,2>;c)版本专用<1,301>；

## 控制器编写接口的参数规定：
> 接口请求参数：
+ app_version：请求该接口的app版本(类似写法1.13)；
+ app_class：请求该接口的设备类型(小写：ios、android、web、wx)；
+ user_id：
+ user_token：
+ 其他1...；
+ 其他2...；

## laravel项目部署(cd到composer.json同目录)
+ 将/文档与说明/里面的vendor文件直接解压在laravel-fyadmin/目录
+ 检查是否安装composer
> composer --version
+ 更换composer镜像
> composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
+ 初始化composer
> composer install
+ 有报错就解决一下报错，然后再次运行composer install，一般会报错1.文件引入的错误；2.vendor依赖的错误。
+ 运行composer update来检测或者今后检测包更新
> composer update
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
> 然后运行composer update命令即可升级版本或插件

# 祝你又帅又有钱！  

##  
## 大更新： 2019-09-19  ， 2019-09-28  ， 2019-10-14
## laravel-fyadmin遵循MIT，帮助别人就是帮助自己，然后一起建设共产主义。
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
