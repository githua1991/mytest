<?php
	date_default_timezone_set('prc'); 
	$temp = strtotime('+3 month', strtotime( '2016-04'));
	echo date( 'Y-m',$temp );
