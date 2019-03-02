<?php
	function getMonthNum($date1,$date2){
		$date1_stamp=strtotime($date1);
		$date2_stamp=strtotime($date2);
		list($date_1['y'],$date_1['m'])=explode("-",date('Y-m',$date1_stamp));
		list($date_2['y'],$date_2['m'])=explode("-",date('Y-m',$date2_stamp));
		return abs($date_1['y']-$date_2['y'])*12 +$date_2['m']-$date_1['m'];
	}
	   
	echo getMonthNum("2013-02-01","2014-02-01").'</br>';