<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>
        提交文件
    </title>

</head>
<body>

<h2>上传多种文件</h2>
<!--enctype 提交表单时要使用哪种内容类型-->
<form action="http://localhost/laravel58/public/index.php/kit/upload_form_file" method="post" enctype="multipart/form-data">
    <label>上传文件：.mp3、.js、.css、.png、.jpg、.jpeg、.gif</label>
    <input type="file" name="file" id="file"><br>
    <br><br>
    <input type="submit" name="submit" value="提交">
</form>
</body>
</html>
