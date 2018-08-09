<?php
return [
//	  应用调试模式
    'app_debug'              => true,
//	'name'=>'cd',
//	'time'=>'2018-06-29'
//'app_status'=>'home',
	'captcha' => [
			//验证码字符集合
			'codeSet' => '2345678abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
			//验证码字体大小(px)5.
			'fontSize' => 20,
			//是否画曲线
			'useCurve' => true,
			//验证码图片高度
			'imageH' =>100,
			//验证码图片宽度
			'imageW' =>150,
			//验证码位数
			'length' =>1,
			//验证码成功后是否重置
			'reset' => true
		],
];
?>