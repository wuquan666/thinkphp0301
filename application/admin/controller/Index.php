<?php
namespace app\admin\controller;
use app\admin\controller\Commond;
use think\Controller;

class Index extends Commond{
	function index(){
		return $this->fetch();
	}
}	
?>