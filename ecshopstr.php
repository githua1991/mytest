<?php
function sub_str($str, $length = 0, $append = true){
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength){
        return $str;  //截取长度等于0或大于等于本字符串的长度，返回字符串本身
    }elseif ($length < 0) { //如果截取长度为负数
        $length = $strlength + $length;//那么截取长度就等于字符串长度减去截取长度
        if ($length < 0){
            $length = $strlength;//如果截取长度的绝对值大于字符串本身长度，则截取长度取字符串本身的长度
        }
    }

    if (function_exists('mb_substr')){
        $newstr = mb_substr($str, 0, $length, EC_CHARSET);
    }elseif (function_exists('iconv_substr')){
        $newstr = iconv_substr($str, 0, $length, EC_CHARSET);
    }else{
        //$newstr = trim_right(substr($str, 0, $length));
        $newstr = substr($str, 0, $length);
    }

    if ($append && $str != $newstr){
        $newstr .= '...';
    }

    return $newstr;
}