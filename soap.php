<?php
	$ws = 'http://14.18.22.5:443/Gateway/services/HelloWord?wsdl';
	$client = new SoapClient( $ws , array( "trace" => true, "connection_timeout" => 200 ) );

	//var_dump ( $client->__getFunctions () );//获取服务器上提供的方法
	
	//var_dump ( $client->__getTypes () );//获取服务器上数据类型 )
	//exit;
	$param = Array(
    'partner' => '100000000000001',
    'sign_type' => 'MD5',
    'service_version' => '1.0',
    'input_charset' => 'GBK',
    'sign' => '771ED524CF8C426A77FA2A784F630221'
	);
	try{
		$res = $client->__call('mchinfoquery',$param);
	}catch(SoapFault $fault){
		var_dump(  $fault );
	}
	var_dump( $res );