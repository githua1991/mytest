<?php
	$str = '';
	for( $i=1;$i<=150000;$i++ ){
		$str .= 'mobiles[]='.$i.'&';
	}
	$str .= 'content=真的好吗';
	
	$post_data = $str;
	$url       = 'www.suhua.com/TESE/ws.php';
	$ch = curl_init ();
	
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	print_r( $return );


