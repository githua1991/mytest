<?php
	 $str = '[dwd]坦克世界[wdwdw]AMX 50B [睡觉][Thanks][Thanks]'; 
	 $str = preg_replace ( '/\\[([^\\]]+)\\]/' ,  '' ,  $str );
	 echo $str;