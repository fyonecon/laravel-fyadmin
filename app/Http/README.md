## 《框架处理Request请求》
+ api请求过程：request--middleware拦截--安全检测拦截器(这里是根据多域名而特殊制作的安全拦截)--Controller--返回正常或者拦截的结果
