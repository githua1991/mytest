<?php 
	class  my_runder{
		
		public static function myway(&$val){
			 $b = 2;
			$val = $val +2;
			//return $data;
		}
	}
	$a = 5;
	$data = my_runder::myway($a);
	var_dump($a);
	$data1 = my_runder::myway($a);
	var_dump($a);