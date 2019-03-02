<?php

	$arr = array(1,2,5,6,3,88,4,9,2,77,3);

	//require_once 'getRand.php';


	//快速排序函数
	function quick_sort($arr){
		//找到递归出口，数组元素为1的时候就终止使用递归
		$len = count($arr);
		if($len <= 1) return $arr;

		//找递归点
		//建立两个数组，left用来保存比元素小的值，right用来保存比元素大的值
		$left = array();
		$right = array();

		//得到left和right数组
		$temp = $arr[0];
		for($i = 1;$i < $len;$i++){
			//每一个元素都与下标为0的元素进行比较
			if($arr[$i] > $temp){
				//保存比第一个元素大的值
				$right[] = $arr[$i];
			}else{
				//保存比第一个元素小或者相等的值
				$left[] = $arr[$i];
			}
		}
	
		//使用递归对左右数组进行排序
		$left = quick_sort($left);
		$right = quick_sort($right);

		//返回排序好的数组
		//array_merge()不会将索引数组进行覆盖，只会在后面添加
		return array_merge($left,array($temp),$right);
	}
	$first = time();
	$res = quick_sort($arr);
	$second = time();
	echo $second - $first;
	echo '<br/>';
	var_dump( $res );