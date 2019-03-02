<?php
	$res = parse_url('www.baidu.com?id=1,23,4&key=333');
	 parse_str  ( $res['query'] , $query);
	var_dump( $query );