<?php
  //echo implode('*',['1','2','4']); 

$val = '好%好';
 $arrlist = explode('%', $val);

var_dump(count($arrlist),count(array_unique($arrlist)));