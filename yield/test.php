<?php
	function gen() {
		$ret = (yield 'yield1');
		//$ret = (yield 'yield2');
		var_dump($ret);
	}
 
	$gen = gen();
	//echo $gen->current();    // string(6) "yield1"
	$gen->send('ret1'); // string(4) "ret1"   (the first var_dump in gen)
	//echo $gen->current();
	
								  // string(6) "yield2" (the var_dump of the ->send() return value)
	//var_dump($gen->send('ret2')); //