<?php
header("Content-type:text/html;charset=utf-8");
require("config.php");
/*定义网站根目录*/
//defined('__BASE__') or define('__BASE__' , 'http://www.youanbao.local/admin/');

ob_end_flush();
//初始化变量
$cookie_file       = dirname(__FILE__).'/cookie.txt';
$cookie_file_login = dirname(__FILE__).'/cookie_log.txt';

$login_url       = __BASE__ ."public/login";
$verify_code_url = __BASE__ ."public/captcha";
 
echo "正在获取COOKIE...<br/><hr/>";
flush();

$curl = curl_init();
$timeout = 5;
curl_setopt($curl, CURLOPT_URL, $login_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($curl, CURLOPT_COOKIEJAR,$cookie_file); //获取COOKIE并存储
$contents = curl_exec($curl);
curl_close($curl);
 
echo "COOKIE获取完成，正在获取验证码...<br/><hr/>";
flush();//
//取出验证码

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $verify_code_url);
curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$img = curl_exec($curl);
curl_close($curl);
 
$fp = fopen("verifyCode.jpg","w");
fwrite($fp,$img);
fclose($fp);

echo "验证码取出完成<br/><hr/>";
echo '<form action="Curl_Session.php" method="POST">
		  <p>账号: <input type="text" name="name" /></p>
		  <p>密码: <input type="password" name="password" /></p>
		  <p>验证码：<input type="text" name="yzm" /></p>
		  <p><img src="verifyCode.jpg"></P>
		  <input type="submit" value="提交表单" />
	  </form>';

echo "<a href='Curl_Session.php?type=chat'>测试手机端压力<a/>";
//停止运行20秒
/*sleep(20);
flush();//
 
echo "休眠完成，开始取验证码...<br/><hr/>";
$code = file_get_contents("code.txt");
echo "验证码成功取出：$code<br/><hr/>";

echo "正在准备模拟登录...<br/><hr/>";
 
$post = "account=youanbao&password=123456&captcha=$code";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($curl, CURLOPT_COOKIEJAR,$cookie_file_login); //获取COOKIE并存储
$result=curl_exec($curl);
curl_close($curl);

$result = json_decode ($result,true);
//这一块根据自己抓包获取到的网站上的数据来做判断
if( $result['status'] == 2){
  echo "登录成功<br/><hr/>";
}else{
  echo "登录失败<br/><hr/>";
}*/
