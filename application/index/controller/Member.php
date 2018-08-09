<?php
namespace app\index\controller;

use think\Controller;
use think\Validate;
use app\index\model\Member as User;
class Member extends Controller{

	public function login(){
		if(request()->isAjax()){
			$data = input('post.');
			$info = ['code'=>0,'info'=>false,'msg'=>''];
			$validate = $this->validate($data,[
				'name'=>'require',
				'pwd'=>'require',
			],[
				'name.require'=>'用户名不能为空',
				'pwd.require'=>'密码不能为空',
			]);
			if(true === $validate){
				
				$data['pwd'] = md_crypt($data['pwd']);
				$model = new User();
				$result = $model->where($data)->find();
				if($result){
					session('name',$result->name);
					session('nameId',$result->id);
					$info = ['code'=>1,'info'=>true,'msg'=>'登录成功'];
					return $info;
				}else{
					$info = ['code'=>0,'info'=>false,'msg'=>'登录失败'];
					return $info;
				}
			}else{
				$info['msg'] = $validate;
				return $info;
			}
		}
		$this->assign('title','登陆');
		return $this->fetch();
	}
	
	public function register(){
		if(request()->isAjax()){
			$data = input('post.');
//			print_r($data);die;
			$info = ['code'=>0,'info'=>false,'msg'=>''];
			$validate = $this->validate($data,[
				'name'=>'require',
				'pwd'=>'require|confirm',
			],[
				'name.require'=>'用户名不能为空',
				'pwd.require'=>'密码不能为空',
				'pwd.confirm'=>'两次密码输入不一致',
			]);

			if(true === $validate){
				
				$data['pwd'] = md_crypt($data['pwd']);
//				print_r($data['pwd']);die;
//				unset($data['pwd_confirm']);
//				$result = db('member')->insert($data);
				$model = new User();
				$result = $model->allowField(true)->save($data);
				if($result){

					$info = ['code'=>1,'info'=>true,'msg'=>'注册成功'];
					return $info;
				}else{
					$info = ['code'=>0,'info'=>false,'msg'=>'注册失败'];
					return $info;
				}
			}else{
				$info['msg'] = $validate;
				return $info;
			}
		}
		$this->assign('title','注册');
		return $this->fetch();
	}
	
	//退出
	public function logout(){
		session(null);
		$this->error('退出成功','Index/index');
	}
	
	//用户中心
	public function index(){
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'clist'=>$clist,
			'title'=>'用户中心'
		]);
		return $this->fetch();
	}
	//用户地址
	public function address(){ 
		if(request()->isAjax()){
			$info = ['code'=>0,'info'=>false,'msg'=>''];
			$data = input('post.');
//			print_r($data);die;
			$validate = $this->validate($data,[
				'province'=>'require',
				'city'=>'require',
				'country'=>'require',
				'address'=>'require',
				'linkman'=>'require',
				'mobile'=>'require',
			]);

			if(true === $validate){
				$data['mid'] = session('nameId');
				$result = db('address')->insert($data);
//				print_r($data);die;
				if($result){
					$info = ['code'=>500,'info'=>true,'msg'=>'添加成功'];
					return $info;
				}else{
					$info = ['code'=>401,'info'=>false,'msg'=>'添加失败'];
					return $info; 
				}
			}else{
				$info['msg'] = $validate;
				return $info;
			}
		}
		$map['mid'] = session('nameId');
		$list = db('address')->where($map)->paginate(2);
//		print_r($list);die;
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'list'=>$list,
			'clist'=>$clist,
			'title'=>'地址管理'
		]);
		return $this->fetch();
	}

	public function myorders(){
		$mid = session('nameId');
		$list = db('orders')
			->alias('o')
			->field('o.*,p.name,p.thumb,p.price as punit')
			->join('__PRODUCT__ p','o.pid=p.id','LEFT')
			->where("mid=$mid")
			->paginate(3);
		foreach($list as $k=>$v){
			$v['thumb']=substr($v['thumb'],1);
		}
//		print_r($list);die;
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'list'=>$list,
			'clist'=>$clist,
			'title'=>'订单详情'
		]);
		return $this->fetch();
	}
}
