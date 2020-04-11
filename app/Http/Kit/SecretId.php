<?php

/*
 *  《PHP对称加密算法-面向加密与解密id》
 *   最大id=9兆
 *
 * */

namespace App\Http\Kit;

use Exception;

class SecretId {

    /*
     *  《PHP对称加密算法-面向加密与解密id》
     *   最大id=9兆，约94904268900006共14位
     *
     *   id=正整数
     *
     *   用于加密 秒时间戳、毫秒时间戳、小于9兆的id、
     *
     * */

    // 自定义的盐，合适的数字会容纳更多的生成id
    protected $k = 81; // [60, 100)
    protected $x = 11; // (10, 30]
    protected $y = 103; // [100, 193)

    // 生成新id编码
    public function en_load_id($id){

        if ($id == '' || $id < 0){
            $id = 0;
        }

        $id  = $id*8;

        $n = $id%3;
        $m = floor($id/3);

        $k = $this->k;
        $x = $this->x;
        $y = $this->y;

        if ($n == 0){
            $a = $b = $c = $m;
        }else if ($n == 1){
            $a = $m + 1;
            $b = $m;
            $c = $m;
        }else if ($n == 2){
            $a = $m + 1;
            $b = $m + 1;
            $c = $m;
        }else{
            $a = $b = $c = 0;
        }

        $a = $a*$k*7 + $k*4;

        $b = $b + $x;
        $b = $b*13;

        $c = $c + $y + $b;
        $c = $c*$y*3;

        return [$a, $b, $c];
    }

    // 解析id编码
    public function de_load_id($a, $b, $c){

        $k = $this->k;
        $x = $this->x;
        $y = $this->y;

        $_a = ($a - $k/4)/$k/7;

        $_b = $b/13 - $x;

        $b = $_b + $x;
        $b = $b*13;

        $_c = $c/3/$y - $y - $b;

        $id = $_a + $_b + $_c;
        $id = $id/8;
        return floor($id);
    }

    // 生成load_id，id为正整数，最大id=9兆，14位约94904268900006
    public function id_encode($id=0, $suffix='html'){

        $load_id = $this->en_load_id($id);

        $a = $load_id[0];
        $b = $load_id[1];
        $c = $load_id[2];

        $load_id = 'aid'.$a.'-bid'.$b.'-cid'.$c.'-'.$suffix;

        return $load_id;
    }

    // 解析load_id
    public function id_decode($load_id, $suffix = 'html'){
        // aid123-bid999-cid10086-html
        $array = explode('-', $load_id);

        if (count($array) == 4){
            $txt1 = substr($array[0], 0, 3);
            $txt2 = substr($array[1], 0, 3);
            $txt3 = substr($array[2], 0, 3);
            $html = $array[3];

            if ($html == $suffix){
                $ids = [$txt1, $txt2, $txt3];
                $a_id = substr($array[array_search('aid', $ids, true)], 3);
                $b_id = substr($array[array_search('bid', $ids, true)], 3);
                $c_id = substr($array[array_search('cid', $ids, true)], 3);
                // var_dump([$a_id, $b_id, $c_id]);
                $id = $this->de_load_id($a_id, $b_id, $c_id);
            }else{
                $id = 0;
            }
        }else{
            $id = 0;
        }

        return $id;
    }



}
