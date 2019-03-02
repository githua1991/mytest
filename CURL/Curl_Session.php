<?php
require("muti_curl_class.php");
require("config.php");

header("Content-type:text/html;charset=utf-8");

//defined('__BASE__') or define('__BASE__' , 'http://www.youanbao.local/admin/');

$name     = isset( $_POST['name'] )     ? $_POST['name'] :'';
$password = isset( $_POST['password'] ) ? $_POST['password'] :'';
$yzm      = isset( $_POST['yzm'] )      ? $_POST['yzm'] : '';

$type     = isset( $_GET['type'] ) ? $_GET['type'] : 'admin';

if( $type != 'chat' ){
	$url  = __MURL__;
}else{
	$url  = __CURL__;
}

if(empty($url)){
	echo '没有访问的网站！';
	exit;
}

$pattern   ="/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\/?%&=]*)?$/i";

$res1 = preg_match($pattern, $url);
if(empty($res1)){
	echo 'url错误!';
	exit;
}

if( $type != 'chat' ){

	$cookie_file       = dirname(__FILE__).'/cookie.txt';
	$cookie_file_login = dirname(__FILE__).'/cookie_log.txt';

	/*访问的网站*/
	//http://www.youanbao.local/admin/equip/ajaxEquipData/id/25
	//$url       = __BASE__ ."about/appHelpClassify?id=";//访问的ID

	/*登录的网站*/
	$post_url  = __BASE__ ."public/CheckLoginData";

	/*POST的数据*/
	$post_data = "account=$name&password=$password&captcha=$yzm";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $post_url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($curl, CURLOPT_COOKIEJAR,$cookie_file_login); //获取COOKIE并存储
	$con = curl_exec($curl);
	curl_close($curl);
	$result = json_decode ($con,true);
	var_dump($result);
	if( (int)$result['status'] == 1){
		echo "登录失败!<br/><hr/>";
		exit;
	}else{
		echo "登录成功!<br/><hr/>";
	}
}
// +----------------------------------------------------------------------
// | 登录成功以后 
// +----------------------------------------------------------------------
	if( $type != 'chat'){
		$option_setting = array(
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 0,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_COOKIEFILE     => $cookie_file_login,
			CURLOPT_HEADER         => false,
		);
	}else{
		$option_setting = array(
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 0,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_HEADER         => false,
		);
	}
	$sucesesnum  = 0;
	$school_id   = array();
	$thread_size = 50;

	if($thread_size < 5){$thread_size = 5;}
	if($thread_size > 300){$thread_size = 300;}

	$btime = time();

	$rc = new muti_curl("request_callback");
	$rc->timeout = 5;
	$rc->thread_size = $thread_size;

	for ($i = 1; $i <= __NUM__; $i++) {
		$the_url = $url;
		$post_data = '';//"id=&garbage=myinputrand_data";
		$request = new request_setting($the_url, $method = "GET", $post_data = $post_data,$header= null, $option_setting);
		$rc->add($request);
	}

	$rc->execute();

	$etime = time();

	$usedtime = $etime - $btime;
	echo 'all :'. $sucesesnum.', use :'. $usedtime.'S'; 
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
	echo "<br>返回数据存在res/里: <br> ";
	
	$path = 'res/'.date('Ymd-H').'_response.log';
	if( !is_dir( dirname($path) ) ){
         mkdir( dirname($path).'/', 0777, TRUE );
	}
    
	$response = '***************************开始*************************************'."\r\n"
				.$response
				.'-------------------------第'.$sucesesnum.'次---------------------------'."\r\n";

	file_put_contents( $path , $response, FILE_APPEND );

    $sucesesnum++;
    echo "<hr>";
}