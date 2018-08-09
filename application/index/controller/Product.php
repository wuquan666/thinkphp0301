<?php
namespace app\index\controller;

use think\Controller;

class Product extends Controller{

	public function proList(){
		//导航
		$clist = db('category')->where("fid=2")->select();
		//子分类
		$id = input('id');
		$cone = db('category')->where('id='.$id)->find();
		$childlist = db('category')->where('fid='.$id)->select();
		//品牌
		$blist = db('brand')->select();
		//商品

		$childid = $this->childId($id);
		$map['cid'] = ['in',$childid];
		$one = db('category')->where("id=$id")->find();
//		print_r($one);die;
		//排序
		if(input('order')){
			switch (input('order')){
				case 'price':
					$order = 'price desc';
					$plist = db('product')->where($map)->order($order)->paginate(2);
					break;
				case 'sales':
					$order = 'sales desc';
					$plist = db('product')->where($map)->order($order)->paginate(2);
					break;	
				default:
					
					break;
			}
		}else{
			$plist = db('product')->where("cid=$id")->paginate(2);
		}
		//选择品牌
		$bid = input('bid');
		if(input('bid')){
			$plist = db('product')->where("cid=$id and bid=$bid")->paginate(2);
		}
		$this->assign([
			'one'=>$one,
			'plist'=>$plist,
			'cone'=>$cone,
			'clist'=>$clist,
			'childlist'=>$childlist,
			'blist'=>$blist,
			'title'=>'商品列表'
		]);
		 return $this->fetch();
	}
	
	function childId($id=0,$list=[],$spec=0){
		if($spec==0) $list[]=$id;
		$fid=$id;
		$parentlist = db('category')->where("fid=$fid")->select();
		foreach($parentlist as $k=>$v){
			$list[] = $v['id'];
			$list =  $this->childId($v['id'],$list,$spec+1);
		}
		return $list; 
	}
	
	public function detail(){
		$id = input('id');
		$product =  db('product')->where('id='.$id)->find();
		$product['picture'] = explode(',', $product['picture']);
//		print_r($product);die; 
		//导航
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'product'=>$product,
			'clist'=>$clist,
			'title'=>'商品详情'
		]);
		return $this->fetch();
	}
	//搜索
	public function search(){
		$keyword = input('keyword');
		$map['name'] = ['like','%'.$keyword.'%'];
		$plist = db('product')->where($map)->paginate(2);
//		print_r($plist);die;
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'plist'=>$plist,
			'clist'=>$clist,
			'title'=>'搜索'
		]);
		return $this->fetch();
	}
}
