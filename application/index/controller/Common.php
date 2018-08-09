<?php
namespace app\index\controller;

use think\Controller;

class Common extends Controller{

	public function __construct(){
		parent::__construct();
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'clist'=>$clist,
		]);
	}
}
