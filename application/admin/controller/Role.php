<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;

class Role extends Commond{
	public function roleList(){
		$list = Db::name('role')->select();
		$levelList = Db::name('level')->field('id,name')->select();
		$levelName = array();
		foreach($list as $k=>$v){
			$idArr = explode('|',$v['level']);
			foreach($levelList as $kk=>$vv){
				if(in_array($vv['id'],$idArr)){
					$levelName[] = $vv['name'];
				}
			}
//			print_r($list[$k]['level']);die;
			$list[$k]['level'] = implode('|',$levelName);
			
		}
		$this->assign('list',$list);
		$this->assign('title','角色列表');
		return $this->fetch();
	}
	
	public function roleAdd(){
		if(request()->isPost()){
			$data = input('post.');
//			print_r($data);die;
			$validate = $this->validate($data,[
				'name'=>'require',
				'level'=>'require',
			]);
			if(true === $validate){
				$data['level'] = implode("|",$data['level']);
				$result = Db::name('role')->insert($data);
				if($result){
					$this->success('添加成功','Role/roleList');die;
				}else{
					$this->error('添加失败');die;
				}
			}else{
				$this->error($validate);
			}
		}
		$list = $this->level();
//		print_r($list);die;
		$this->assign('list',$list);
		$this->assign('title','角色添加');
		return $this->fetch();
	}
	
	public function roleUpdate(){
		$id=input('id');
		if(request()->isPost()){
			$data = input('post.');
//			print_r($data);die;
			$validate = $this->validate($data,[
				'name'=>'require',
				'level'=>'require',
			]);
			if(true === $validate){
				$data['level'] = implode("|",$data['level']);
//				print_r($data);die;
				$result = Db::name('role')->where("id=$id")->update($data);
				if($result){
					$this->success('编辑成功','Role/roleList');die;
				}else{
					$this->error('编辑失败');die;
				}
			}else{
				$this->error($validate);
			}
		}
		
		$one = Db::name('role')->where('id='.$id)->find();
		$one['level'] = explode('|', $one['level']);
//		print_r($one);die;
		$this->assign('one',$one);
		$list = $this->level();
//		print_r($list);die;
		$this->assign('list',$list);
		$this->assign('title','品牌编辑');
		return $this->fetch();
	}
	
	public function roleDelete(){
		$id=input('id');
		$result = Db::name('role')->delete($id);
		if($result>0){
			$this->success('删除成功','Role/roleList');die;
			}elseif($result===0){
				$this->success('未删除数据');die;
			}else{
				$this->success('删除失败');die;
			}
	}
}


	
?>