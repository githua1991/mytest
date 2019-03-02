<?php
	class config{
		protected $_Config =array(
			'http'=>'curl',
			'name'=>'zhangjie'
		);	
		
		public function __set($c, $v){
			$this->_Config[$c] = $v;
		}
	
		public function __get($c){
			return $this->_Config[$c];
		}
	}

	class myclass{
		public function __construct($Config=NULL){
			$this->Config = new config;
		}

		public function usetool(){
			return $this->http;
		}
	}

	$oModel = new myclass;
	$res = $oModel->usetool();
	echo $res;