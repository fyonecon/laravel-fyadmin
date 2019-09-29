

<div>

    <script src="http://cdnaliyun.oss-cn-hangzhou.aliyuncs.com/js/jquery-1.11.3.min.js"></script>

    <input type="file" class="file-input-style" onchange="get_that_file(this, true, true);"/>

    <script>

        // js截取字符串特定[字符]前、后、之间的特定一段
        // match_string("data:application/octet-stream;base64", "between", [":", ";"])
        function match_string(string, location, array) {
            if (typeof array !== "object"){
                console.log("注意array格式为数组Object。");
                return ;
            }
            let str = string;
            if (location === "before"){

            }else if (location === "after"){

            }else if (location === "between"){
                let a = str.indexOf(array[0]);
                let b = str.indexOf(array[1]);
                str = str.substr(a+1, b-a-1);
            }else {
                console.log("location取值为before、after、between。");
            }

            return [str, location];
        }

        function get_that_file(that, log, post) {
            let has_type = ["png", "jpg", "jpeg", "gif", "bmp", "css", "javascript", "plain", "octet-stream", "json", "mp3", "mp4", "vnd.openxmlformats-officedocument.wordprocessingml.document", "vnd.openxmlformats-officedocument.presentationml.presentation", "vnd.openxmlformats-officedocument.spreadsheetml.sheet", "zip", "ico"];
            let file = that.files;
            for (let i=0; i<file.length; i++){
                let reader = new FileReader();
                reader.readAsDataURL(file[i]); // 转为文件+参数
                reader.onload = function(e) {
                    let file_name = file[i].name;
                    let file_type = file[i].type.split("/");
                    let file_size = file[i].size;
                    let file_base64 = e.target.result;
                    if (file_type.length !== 2){
                        file_type = file_base64.match(/data:(\S*);base64/)[1].split("/"); // 直接截取特定字符串之间的内容
                        console.log("未知的文件类型="+i+"="+file_type);
                    }
                    let file_info = [];
                    let msg = "";
                    if(has_type.indexOf(file_type[1]) > -1){ // 该格式属于白名单格式
                        file_info = [
                            i,
                            file_name,
                            file_type[0],
                            file_type[1],
                            file_size,
                            file_base64,
                        ];
                        if (log){ // 是否打印日志
                            console.log("文件信息 i="+i);
                            console.log(file);
                            console.log(e);
                            console.log(file_info);
                        }
                        msg = "白名单文件格式";
                    }else {
                        console.log("未知格式的文件 i="+i+"；type="+file_type[1]);
                        file_info = [];
                        msg = "未知文件格式";
                    }

                    if (post){
                        //upload_that_file(file_info, msg);
                    }else {
                        console.log("跳过。");
                    }

                };
            }
        }

        function upload_that_file(file_info, msg) {
            console.log(file_info);
            let _file_info = "";
            if (file_info.length === 0){
                alert(msg);
                return;
            }else {
                _file_info = file_info.join("#&#");
            }

            // 干其他
            /*请求数据*/
            $.ajax({
                url: "http://localhost/laravel58/public/index.php/kit/upload_base64_file",
                type: "POST",
                dataType: "json",
                async: true,
                data: { // 字典数据
                    file_info: _file_info,
                },
                success: function(back, status){

                    // 数据转换为json
                    var datas = data = "";
                    if(typeof back === "string"){
                        datas = JSON.parse(back);
                        data = back;
                    } else {
                        datas = back;
                        data = JSON.stringify(back)
                    }
                    console.log("类型：" + typeof back + "。\n数据：" + data +"。\n状态：" + status + "。");

                    // 解析json
                    if (datas.state===0){
                        console.log(datas.msg);


                    }else if (datas.state===1) {
                        console.log(datas.msg);


                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                    console.log("接口请求错误或者网络不通");
                }
            });

        }


    </script>

</div>
