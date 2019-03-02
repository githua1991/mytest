<?php
	// 输出运行中的 php/httpd 进程的创建者用户名
	// （在可以执行 "whoami" 命令的系统上）
	exec('cmd ipconfig',$output);
	var_dump( $output );
?> 