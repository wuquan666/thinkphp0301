<?php
namespace app\admin\controller;
use think\Loader;
use app\admin\model\User;
use think\Db;

class Manager extends Commond{
	public function manList(){
		$list = Db::name('manager')
		->alias('m')
		->field('m.*,r.name as rname')
		->join('tp_role r','m.rid=r.id','LEFT')
		->order('id desc')
		->paginate(2);
		$this->assign('list',$list);
//		print_r($list);die;
		$this->assign('title','管理员列表');
		return $this->fetch();
	}
	
	public function manAdd(){
		if(request()->isPost()){
			$data = input('post.');
//			print_r($data);die;
			$validate = $this->validate($data,[
				'rid'=>'gt:0',
				'uname'=>'require',
				'pwd'=>'require|confirm',
			],[
				'rid.gt'=>'请选择角色',
				'uname.require'=>'账号不能为空',
				'pwd.require'=>'密码不能为空',
				'pwd.confirm'=>'两次输入密码不一致',
			]);
			$uname = $data['uname'];
			$hasName = Db::name('manager')->where("uname='$uname'")->find();
//			print_r($hasName);die;
			if($hasName) $this->error('用户名已存在');
			if(true === $validate){
				$data['pwd'] = md5(crypt($data['pwd'],config('pwdstring')));
				$model = new User();
//				print_r($model);die;
				$result = $model->allowField(true)->save($data);
				if($result){
					$this->success('添加成功','Manager/manList');die;
				}else{
					$this->error('添加失败');die;
				}	
			}else{
				$this->error($validate);
			}
		}
		$onlist = Db::name('role')->field('id,name')->select();
//		print_r($onlist);die;
		$this->assign('onlist',$onlist);
		$this->assign('title','管理员添加');
		return $this->fetch();
	}
	
	public function manUpdate(){
		$id=input('id');
		if(request()->isPost()){
			$data = input('post.');
			$validate = $this->validate($data,[
				'uname'=>'require',
			]);
			if(true === $validate){
				$data['pwd'] = md5(crypt($data['pwd'],config('pwdstring')));
				$model = new User();
//				print_r($model);die;
				$result = $model->allowField(true)->save($data,['id'=>$data['id']]);
				if($result){
					$this->success('编辑成功','Manager/manList');die;
				}else{
					$this->error('编辑失败');die;
				}	
			}else{
				$this->error($validate);
			}
			
		}
		
		$one = Db::name('manager')->where('id='.$id)->find();
//		print_r($one);die;
		$this->assign('one',$one);
		$onlist = Db::name('role')->field('id,name')->select();
//		print_r($onlist);die;
		$this->assign('onlist',$onlist);
		$this->assign('title','管理员编辑');
		return $this->fetch();
	}
	
	public function manDelete(){
		$id=input('id');
		$result = Db::name('manager')->delete($id);
		if($result>0){
			$this->success('删除成功','Manager/manList');die;
			}elseif($result===0){
				$this->success('未删除数据');die;
			}else{
				$this->success('删除失败');die;
			}
	}
}


	
?>