<?php
	$arr = array(
		'1' => array('style'=>'左右风格a','url'=>'system/appstyle/style1.png'),
		'2' => array('style'=>'左右风格b','url'=>'system/appstyle/style2.png'),
		'3' => array('style'=>'上下风格','url'=>'system/appstyle/style3.png'),
		'choice' => '1',
	);
	$ser =  serialize( $arr );

	//$unser = unserialize( $ser );

	echo $ser;