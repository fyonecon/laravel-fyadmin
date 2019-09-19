<?php

/*
 *  《PHP对称加密算法》
 * */

namespace App\Http\Controllers\Kit;

use App\Http\Controllers\Controller;

class Secret extends Controller{

    /*
     * php对称加密算法，根据字母对照表
     * 因为最终加密的字符串长度会x2，因此，加密场景适合加密字符串不是特别巨大的地方。
     * 代码一共有三处自定义地方，根据注释更改自定义内容。
     * 现有加密破解差不多需要万(亿)亿次。
     * 2019-03-23
     * github.com/fyonecon
     *
     * 加密内容格式：字母、数字、中文等任意内容，并支持部分特殊符号
     * 加密：new Secret()->encode('');
     * 解密：new Secret()->decode('');
     *
     * */

    private $secret_key = 'fya19'; // 自定义。 密钥：字母、数字、中文等任意内容
    private $salt = 17; // 自定义。 容错度：数字范围[0, 63]
    private $s = 1; // 不要更改

    protected $_replace = [ // 自定义。 建议使用不一样的[0]对应[1]，密钥对照表
        ['=', 'oai1c'], // 必须
        ['/', 'gej1n'], // 必须
        ['+', 'hbua1'], // 必须
        ['3', '2nvge'],
        ['7', '16lo0'],
        ['8', 'uhws4'],
        ['9', 'i00o6'],
        ['L', 'l0ieg'],
        ['M', 'lmvsk'],
        ['W', 'aiinh'],
        ['z', 'e1ifg'],
        ['y', 'a4bcs'],
        ['d', 'al2fj'],
        ['p', 'jaoi0'],
    ];

    public function __construct()
    {
        if (empty($this->secret_key)) {
            $this->secret_key = ceil(time() / 100000) * 100000; // 如果没有定义密钥则使用为期一天的可变密钥
        }
        $this->secret_key = md5($this->secret_key) . sha1($this->secret_key) . md5($this->salt) . sha1($this->salt); // len 144

    }

    public function en_mix($str)
    {
        $key_str = $this->secret_key;
        $len1 = strlen($key_str);
        $len2 = strlen($str);

        $new_array = [];
        if ($len1 <= $len2) {
            for ($i = 0; $i < $len1; $i++) {
                $new_array[] = $str[$i];
                if ($i >= $this->s) {
                    $new_array[] = $key_str[$i];
                }

            }
        } else {
            for ($i = 0; $i < $len2; $i++) {
                $new_array[] = $str[$i];
                if ($i >= $this->s) {
                    $new_array[] = $key_str[$i];
                }
            }
        }

        $string = implode('', $new_array);
        return $string;
    }

    public function de_mix($str)
    {
        $new_array = [];
        for ($i = 0; $i < strlen($str); $i++) {
            if ($i <= $this->s) {
                $new_array[] = $str[$i];

            } else {
                if ($i % 2 != 0) {
                    $new_array[] = $str[$i];
                }
            }

        }

        $string = implode('', $new_array);
        return $string;
    }

    public function to_string($data)
    { // 字符串变“数字 和 字母”字符串
        $new = base64_encode($data);
        $string = '';

        $replace = $this->_replace;

        for ($i = 0; $i < count($replace); $i++) {
            $string = str_replace(@$replace[$i][0], @$replace[$i][1], $new);
            $new = $string;
        }

        return $this->en_mix($string);
    }

    public function to_data($string)
    { // 数字 和 字母”字符串变“字符串
        //$old_string = $string;
        $old_string = $this->de_mix($string);
        $data = '';

        $replace = $this->_replace;

        for ($i = 0; $i < count($replace); $i++) {
            $data = str_replace(@$replace[$i][1], @$replace[$i][0], $old_string);
            $old_string = $data;
        }

        return base64_decode($data);
    }

    public function make_key_value()
    { // 生成一位加密对照表
        $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'; //自定义。 不需要修改内容，但是可以修改字符数字的顺序
        $new_string = $string . $string;
        $salt = $this->salt;

        if ($salt < 0 || $salt > strlen($string)) {
            $salt = ceil(strlen($string) / 3);
        }

        $array1 = [];
        $array2 = [];
        for ($m = 0; $m < strlen($string); $m++) {
            $array1[$string[$m]] = $new_string[$m + $salt];
            $array2[$new_string[$m + $salt]] = $string[$m];
        }

        return [$array1, $array2];
    }

    public function encode($data)
    {
        $new_data = $this->to_string($data);
        $replace = $this->make_key_value()[0];

        $new_data_array = [];
        for ($n = 0; $n < strlen($new_data); $n++) {
            $new_data_array[] = $new_data[$n];
        }

        $array = [];
        for ($i = 0; $i < count($new_data_array); $i++) {
            $key = $new_data_array[$i];
            $value = $replace[$key];
            $array[] = $value;
        }

        $string = implode('', $array);
        return trim($string);
    }

    public function decode($data)
    {
        $new_data = $data;
        $replace = $this->make_key_value()[1];

        $new_data_array = [];
        for ($n = 0; $n < strlen($new_data); $n++) {
            $new_data_array[] = $new_data[$n];
        }

        $array = [];
        for ($i = 0; $i < count($new_data_array); $i++) {
            $key = $new_data_array[$i];
            $value = $replace[$key];

            $array[] = $value;
        }

        $string = implode('', $array);
        $data = $this->to_data($string);
        return trim($data);
    }

}


// 测试算法
//$secret = new \app\admin\controller\Secret();
//
//$res1 = $secret->encode('=你好abc123n');
//echo "加密：" . $res1;
//
//echo '<hr/>';
//
//$res2 = $secret->decode($res1);
//echo "解密：" . $res2;
