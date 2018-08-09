<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;

class Brand extends Commond{
	public function brandList(){
		$list = Db::name('brand')->paginate(2);
		$this->assign('list',$list);
		$this->assign('title','品牌列表');
		return $this->fetch();
	}
	
	public function brandAdd(){
		if(request()->isPost()){
			$data = input('post.');
			$validate = Loader::validate('Brand');
//			print_r($validate);die;
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('brand')->insert($data);
//			print_r($result);die;
			if($result){
				$this->success('添加成功','Brand/brandList');die;
			}else{
				$this->success('添加失败');die;
			}
		}
		$this->assign('title','品牌添加');
		return $this->fetch();
	}
	
	public function brandUpdate(){
		$id=input('id');
		if(request()->isPost()){
			$data = input('post.');
			$validate = Loader::validate('Brand');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('brand')->where("id=$id")->update($data);
			if($result>0){
				$this->success('更新成功','Brand/brandList');die;
			}elseif($result===0){
				$this->success('未更新数据');die;
			}else{
				$this->success('更新失败');die;
			}
		}
		
		$one = Db::name('brand')->where('id='.$id)->find();
//		print_r($one);die;
		$this->assign('one',$one);
		$this->assign('title','品牌编辑');
		return $this->fetch();
	}
	
	public function brandDelete(){
		$id=input('id');
		$result = Db::name('brand')->delete($id);
		if($result>0){
			$this->success('删除成功','Brand/brandList');die;
			}elseif($result===0){
				$this->success('未删除数据');die;
			}else{
				$this->success('删除失败');die;
			}
	}
}


	
?>