<?php
	/**
	*计算物业缴费滞纳金
	*
	*/
	date_default_timezone_set('PRC'); 
	/*function calculateDelayingFee( $months ,$total ,$day=null ){
		
		$now = empty( $day ) ? date('Y-m-d') : $day;		

		$pay_timestamp = strtotime( $months ) ;
		
		$season = ceil(  date( 'n' ,$pay_timestamp ) /3 );//首先计算缴费月份所在季度
		
		//计算不需要要滞纳金的 的起始天
		//$pay_min =  date('Y-m-d', mktime( 0, 0, 0 , $season*3-3+1,1 , date('Y') ) );
		$pay_max =  date('Y-m-d', mktime( 23,59,59 ,$season*3, date('t',mktime(0, 0 , 0,$season*3,1,date("Y",$pay_timestamp))),date('Y',$pay_timestamp)));
		
		//echo $pay_max;exit;
		if( $now > $pay_max ){
			$pay_timestamp =  strtotime('+1 month',$pay_timestamp );
			$now     = strtotime($now);
			$pay_min = strtotime( date('Y-m',$pay_timestamp).'-01' );
			$days    = round( ($now - $pay_min)/86400 );
			echo date( 'Y-m-d',$pay_min );exit;

			$cost =  $total*pow( ( 1 + 0.003),$days ) - $total;
			return number_format( $cost, 2 ,'.','');
		}else{
			return 0;
		}
	}*/

	//echo calculateDelayingFee( '2015-10',970.00);

	/*function judgeTime( $now  = null){
		
    	$now     = empty( $now ) ? time() : strtotime( $now );
    	$season  = ceil(  date( 'n' , $now ) /3 );	
		
		$now_min = mktime( 0, 0, 0 , $season*3-3+1 , 1 , date( 'Y' , $now ) );

		return  date( 'Y-m-d' , strtotime( 'next year -1 day',$now_min ) );
    }

	echo judgeTime('2015-01').'</br>';*/

	//echo round(53336.445344,2);

	/*function pregq($test){  
        $rule ='/^[A-Za-z]+[0-9]+$/';  
        preg_match($rule,$test,$result);  
        return $result;  
    } */
	/*
	echo date('Y-m-d H:i:s','1461909600');
	if( 1461909600 - time() < 24*3600 ){
		//$this->addError( 'error' , '只能在预约时间前24小时才能被取消!' );
		//return false;
		echo '不能';
	}else{
		echo '能';
	}*/
	
	/*function add_push( $mobile_send_done ,$i){
		array_push( $mobile_send_done , $i );
		return $mobile_send_done;
	}
	$mobile_send_done = array();
	for( $i=0;$i<=2;$i++ ){
		$mobile_send_done = add_push($mobile_send_done, $i );
	}
	var_dump( $mobile_send_done);*/
	//echo md5('123456');
	/*$subject = "377@3wj解决8**我   &&7s90\\[]的$$4oo";
	$pattern = '/[\x{4e00}-\x{9fa5}A-Za-z]+/u';
	preg_match_all($pattern, $subject, $matches);

	var_dump( $matches );

	$subject = "333";

	preg_match($pattern, $subject, $matches);
	var_dump( $matches );*/

	//echo md5(md5('cyberway1234') . 'Sq1Fbq20');
	//$arr = unserialize('a:7:{s:36:"b599dca8f14fb1cfdce64d5f39934770__id";s:3:"199";s:38:"b599dca8f14fb1cfdce64d5f39934770__name";s:11:"13763314126";s:37:"b599dca8f14fb1cfdce64d5f39934770model";s:8:"customer";s:34:"b599dca8f14fb1cfdce64d5f39934770id";s:3:"199";s:40:"b599dca8f14fb1cfdce64d5f39934770username";s:11:"13763314126";s:38:"b599dca8f14fb1cfdce64d5f39934770mobile";s:11:"13763314126";s:40:"b599dca8f14fb1cfdce64d5f39934770__states";a:4:{s:5:"model";b:1;s:2:"id";b:1;s:8:"username";b:1;s:6:"mobile";b:1;}}');
	//var_dump($arr);
	//echo  0.03 + 0 - null -0 ;
	
	/*function send( $mobile, $message , $isWait = true ){
        if ( is_array( $mobile ) ) {

            if (count($mobile) > self::SMS_MOBILE_COUNT) {

                return -100;
            }
        }else{
            $mobile = array( $mobile );
        }
        $tele = '';
        foreach( $mobile as $k => $v ){
           $tele .= 'mobiles[]='.$v.'&';
        }
        $curlPost = 'key=kdhudo3478cj450972&'.$tele.'content='.$message;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://internet.rfchina.com/sms/send');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$curlPost);
        $result = curl_exec($ch);
        curl_close($ch);

        $str = json_decode( $result );

        $data = new StdClass();
        $data->PostResult = new StdClass();
        $data->PostResult->result  = ( $str->status == 200 )  ?  0 :  1 ;
        $data->PostResult->message = $str->message ;

        return $data;
    }
	
	
	var_dump( send( '18578608151', '怒好吗' , $isWait = true ) );*/
	//sign=ecf3476ddcb13628c42dd9986dac3458
	//echo md5('/site/login?key=20&ts=1481626076&secret=OnFpFkif5XIOv_tpmjRTR9XGspbZR45X');
	//echo md5('123456',false);
	//echo -1%8;
	echo date('Y-m-d H:i:s','1524415200');
	
	





	