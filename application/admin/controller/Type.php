<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;

class Type extends Commond{
	public function typeList(){
		$list = Db::name('type')->paginate(2);
		$this->assign('list',$list);
		$this->assign('title','类型列表');
		return $this->fetch();
	}
	
	public function typeAdd(){
		if(request()->isPost()){
			$data = input('post.');
			$result = Db::name('type')->insert($data);
//			print_r($result);die;
			if($result){
				$this->success('添加成功','Type/typeList');die;
			}else{
				$this->success('添加失败');die;
			}
		}
		$this->assign('title','品牌添加');
		return $this->fetch();
	}
	
	public function typeUpdate(){
		$id=input('id');
		if(request()->isPost()){
			$data = input('post.');
			$validate = Loader::validate('Type');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('type')->where("id=$id")->update($data);
			if($result>0){
				$this->success('更新成功','Type/typeList');die;
			}elseif($result===0){
				$this->success('未更新数据');die;
			}else{
				$this->success('更新失败');die;
			}
		}
		
		$one = Db::name('type')->where('id='.$id)->find();
//		print_r($one);die;
		$this->assign('one',$one);
		$this->assign('title','品牌编辑');
		return $this->fetch();
	}
	
	public function typeDelete(){
		$id=input('id');
		$result = Db::name('type')->delete($id);
		if($result>0){
			$this->success('删除成功','Type/typeList');die;
			}elseif($result===0){
				$this->success('未删除数据');die;
			}else{
				$this->success('删除失败');die;
			}
	}
}


	
?>