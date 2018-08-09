<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\model\Goods;

class Product extends Commond{
	public function proList(){
		$list = Db::name('product')
		->alias('m')
		->field('m.*,c.name as cname,b.name as bname')
		->join('tp_category c','m.cid=c.id','LEFT')
		->join('tp_brand b','m.bid=b.id','LEFT')
		->order('id desc')
		->paginate(5);
//		print_r($list);die;
		$this->assign('list',$list);
		$this->assign('title','商品列表');
		return $this->fetch();
	}
	
	public function proAdd(){
		if(request()->isAjax()){
			$info = ["code"=>0,"info"=>false,"msg"=>""];
			$data = input('post.');
//			print_r($data);die;
			//上传
			$dataImg = $this->upload('ufile');
//			print_r($dataImg);die;
			if($dataImg){
				//缩略图
//				echo 123;die;
				$thumb = $this->thumb($dataImg[0]);
				//水印图
				$this->water($dataImg[0]);
				
				$data['thumb'] = $thumb;
				$data['picture'] = implode(',',$dataImg);
				
				
			}
			$validate = $this->validate($data,[
				'name' => 'require',
				'cid' => 'require',
				'bid' => 'require',
				'price' => 'require',
			]);
			if(true === $validate){
//				print_r($data);die;
				$pro = new Goods($data);
				
				$result = $pro->allowField(true)->save();
				if($result){
					$info =["code"=>500,"info"=>true,"msg"=>"添加成功"];
					return $info;
				}else{
					$info =["code"=>401,"info"=>false,"msg"=>"添加失败"];
					return $info;
				}
			}else{
				$info['code'] = 400;
				$info['msg'] = $validate;
				return $info;
			}
		}
		//类型
		$typelist = db('type')->select();
		$this->assign('typelist',$typelist);
		//属性
		//分类
		$cateList = $this->cate();
		//品牌
		$brandList = db('brand')->select();
		$this->assign('cateList',$cateList);
		$this->assign('brandList',$brandList);
		$this->assign('title','商品添加');
		return $this->fetch();
	}
	
	public function proUpdate(){
		$id=input('id');
		if(request()->isAjax()){
			$data = input('post.');
//			print_r($data);die;
			//上传
			$dataImg = $this->upload('ufile');
//			print_r($dataImg);die;
			if($dataImg){
				//缩略图
//				echo 123;die;
				$thumb = $this->thumb($dataImg[0]);
				//水印图
				$this->water($dataImg[0]);
				
				$data['thumb'] = $thumb;
				$data['picture'] = implode(',',$dataImg);
				
			}
			$validate = $this->validate($data,[
				'name' => 'require',
				'cid' => 'require',
				'bid' => 'require',
				'price' => 'require',
			]);
			if(true === $validate){
//				echo 15;die;
				$pro = new Goods();
				$result = $pro->allowField(true)->save($data,['id'=>$data['id']]);
				
				if($result>0){  
					$info =["code"=>500,"info"=>true,"msg"=>"编辑成功"];
					return $info;
				}else
				{
//					echo 123;die;
					$info =["code"=>401,"info"=>false,"msg"=>"编辑失败"];
					return $info;
				}
			}else{
				$info['code'] = 400;
				$info['msg'] = $validate;
				return $info;
			}
		}
		//分类
		$cateList = $this->cate();
		//品牌
		$brandList = db('brand')->select();
		$this->assign('cateList',$cateList);
		$this->assign('brandList',$brandList);
		
		$one = Db::name('product')->where('id='.$id)->find();
		//把图片字段变成数组
		$one['picture'] = explode(',',$one['picture']);
//		print_r($one);die;
		$this->assign('one',$one);
//		print_r($one);die;
		$this->assign('title','商品编辑');
		return $this->fetch();
	}
	
	public function proDelete(){
		if(request()->isPost()){
//			print_r(input('post.'));die;
			$data = input('post.');
			if($data['dro'] === 0){
				$this->error('请选择操作');
			}
			switch ($data['dro']) {
				case 'del':
					$idArr = $data['id'];
					$result = Db::name('product')->delete($idArr);
					if($result){
						$this->success('操作成功','proList');
					}else{
						$this->error('操作失败');
					}
					break;
					
					case 'check':
					break;			
			}		
		}
		$id=input('id');
		$result = Db::name('product')->where("id=$id")->delete();
		if($result>0){
			$this->success('删除成功','Product/proList');die;
			}elseif($result===0){
				$this->success('未删除数据');die;
			}else{
				$this->success('删除失败');die;
			}
	}
	
	public function findattr(){
		$typeid = input('typeid');
		$attr = db('attribute')
		->alias('a')
		->field('a.*,o.id as oid,o.name as oname')
		->join('__OPTIONS__ o','a.id=o.aid','LEFT')
		->where(['typeid'=>$typeid])
		->select();
		foreach($attr as $k=>$v){
			$aid = $v['id'];
			$attr[$k]['child'] = db('options')->where(['aid'=>$aid])->select(); 
		}
		return $attr;
	}
}


	
?>