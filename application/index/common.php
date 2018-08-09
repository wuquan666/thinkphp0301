<?php
	function md_crypt($str){
		return md5(crypt($str,config('pwdstring')));
	}
	
	function alipay($orderid,$total,$subject,$body){
		include 'alipay/wappay/mypay.php';
	}
	
	function notify_url(){
		include 'alipay/wappay/notify_url.php';
	}
	
	function return_url(){
		include 'alipay/wappay/return_url.php';
	}
?>