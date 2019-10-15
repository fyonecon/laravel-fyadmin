<?php
/**
 * 需要使用use来调用公共函数；
 * 框架任何文件都可调用；
 * 解决了继承混乱的问题。
 */

trait sys_enhance{

    function test_trait($txt){

        return 'sys_enhance-'.$txt;
    }



}
