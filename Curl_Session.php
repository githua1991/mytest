<?php

$name = isset($_POST['name']) ? $_POST['name'] :'';
$password = isset($_POST['password']) ? $_POST['password'] :'';
$yzm = isset( $_POST['yzm'] ) ? $_POST['yzm'] : '';

$cookie_file = dirname(__FILE__).'/cookie.txt';

header("content-type:text/html;charset='utf-8'");
require("muti_curl_class.php");

//set_time_limit(0);
//$post_url="http://124.172.224.169/curl_session/login.php";//登录的页面
$post_url = 'http://www.youanbao.local/admin/public/CheckLoginData';

//$post_data="user=dfsfdsfs&pwd=dsfdsf";
$post_data = array('account'  => $name,
					'password'=> $password,
					'captcha' => $yzm,
					);

//session_start();
//$strCookie="PHPSESSID=".$_COOKIE['PHPSESSID'];
$strCookie = '';
//session_write_close(); 
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$post_url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_COOKIE, $strCookie);
curl_setopt($ch,CURLOPT_POSTFIELDS,$cookie_file);
$con = curl_exec($ch);
curl_close($ch);
 
//echo $strCookie; 
echo "<br>"; 
echo "<hr/>";

//session_write_close(); 
$sucesesnum=0;
$school_id=array();
$thread_size=50;
if($thread_size<5){$thread_size =5;}
if($thread_size>300){$thread_size =300;}

$url="http://124.172.224.169/curl_session/main.php?id=";//访问的ID

$option_setting = array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
		CURLOPT_COOKIE=>$strCookie,
        CURLOPT_HEADER=>true,
    );

$btime=time();
$rc = new muti_curl("request_callback");
$rc->timeout = $timeout;
$rc->thread_size = $thread_size;
for ($i = 1; $i <= 100; $i++) {
    $the_url=$url.$i;
	$post_data="id=&garbage=myinputrand_data";
    $request = new request_setting($the_url, $method = "GET", $post_data = $post_data,$header= null, $option_setting);
    $rc->add($request);
}

$rc->execute();
$etime=time();
$usedtime=$etime-$btime;
echo 'all'. $sucesesnum.'use'. $usedtime.'S'; 
echo '<hr>';
 


/***********************************************************************************************
   回调函数，用于调试各种结果。
   $response, 代表响应结果，一般来说是一长长的字符串。
   $info      curl获得的信息，里面好多东东 是一个数组形式
   $request   请求的信息，为一堆可变的数组
   
**************************************************************************************************/
function request_callback($response, $info, $request) {
    global $sucesesnum,$school_id;
	echo "<pre>";
    if( $response !== false  ) {
        echo "成功响应<br>";
    }
    echo '<br>第'. $sucesesnum.'成功请求。用时:'. $info['total_time'].'秒'; 
	echo "<br>返回数据如下: <br> ";
	print_r ($response);
    $sucesesnum++;
    echo "<hr>";
}

//$href=$_SERVER['PHP_SELF'];