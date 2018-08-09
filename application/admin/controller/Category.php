<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;

class Category extends Commond{
	public function cateList(){
		$list = $this->cate();
		$this->assign('list',$list);
		$this->assign('title','分类列表');
		return $this->fetch();
	}
	
	public function cateAdd(){
		if(request()->isPost()){
			$data = input('post.');
			
			$result = Db::name('category')->insert($data);
			if($result){
				$this->success('添加成功','Category/cateList');die;
			}else{
				$this->success('添加失败');die;
			}
		}
		
		$list = $this->cate();
		$this->assign('list',$list);
		$this->assign('title','分类添加');
		return $this->fetch();
	}
	
	public function cateUpdate(){
		$id=input('id');
		if(request()->isPost()){
			$data = input('post.');
			$result = Db::name('category')->where("id=$id")->update($data);
			if($result>0){
				$this->success('更新成功','Category/cateList');die;
			}elseif($result===0){
				$this->success('未更新数据');die;
			}else{
				$this->success('更新失败');die;
			}
		}
		
		$one = Db::name('category')->where('id='.$id)->find();
//		print_r($one);die;
		$this->assign('one',$one);
		$list = $this->cate();
		$this->assign('list',$list);
		$this->assign('title','分类编辑');
		return $this->fetch();
	}
	
	public function cateDelete(){
		$id=input('id');
		$result = Db::name('category')->delete($id);
		if($result>0){
			$this->success('删除成功','Category/cateList');die;
		}elseif($result===0){
			$this->success('未删除数据');die;
		}else{
			$this->success('删除失败');die;
		}
	}
}


	
?>