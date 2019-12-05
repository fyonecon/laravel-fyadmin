<?php

header('HTTP/1.1 404 Not Found');
header('Content-Type: text/html; charset=utf-8');
echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">';
echo '<title>404-访问的页面不存在</title>';
echo '<style>body{font-size: 18px;color: #555555;margin: 20px;background: #EEEEEE;font-weight: bold;text-align: center;letter-spacing: 2px;}</style>';
exit('请不要在微信浏览器里面打开此链接。');