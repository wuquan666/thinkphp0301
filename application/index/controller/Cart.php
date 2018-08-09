<?php
namespace app\index\controller;

use think\Controller;

use think\Db;

class Cart extends Controller{

	public function addCart(){
		$info = ['code'=>0,'info'=>false,'msg'=>''];
		//判断是否登陆
		if(!session('?name')){
			$info = ['code'=>400,'info'=>false,'msg'=>'请先登录'];
			return $info;
		}
		//判断购物车中是否存在该商品
		$data['mid'] = session('nameId');
		$data['pid'] = input('pid');
		$num = input('num');
		$isHas = db('cart')->where($data)->find();//查询当前用户当前商品是否存在购物表中
//		print_r($isHas);die;
		if($isHas){
			//存在,改变购物数量
			$result = db('cart')->where('id='.$isHas['id'])->setInc('num',$num);
		}else{
			//不存在，添加一条购物信息
			$data['num'] = $num;
			$data['ctime'] = time();
			$result = db('cart')->insert($data);
//			print_r($result);die;
		}
		if($result>0){
			$info = ['code'=>500,'info'=>true,'msg'=>'已加入购物车'];
			return $info;
		}else{
			$info = ['code'=>401,'info'=>false,'msg'=>'加入失败'];
			return $info;
		}
		
	}
	
	public function cartList(){
		$mid = session('nameId');
		$list = db('cart')
		->alias('c')
		->field('c.*,p.id as pid,p.name,p.thumb,p.price')
		->join('__PRODUCT__ p','c.pid=p.id','LEFT')
		->where("mid=$mid")
		->select();
//		print_r($list);die;
		foreach($list as $k=>&$v){
			$v['thumb'] = substr($v['thumb'],1);
			$v['total'] = $v['num']*$v['price'];
		}
		
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'list'=>$list,
			'clist'=>$clist,
			'title'=>'购物车'
		]);
		return $this->fetch();
	}
	//单个删除
	function onedel(){
		$id = input('id');
//		print_r($id);die;
		$result = db('cart')->delete($id);
//		print_r($result);die;
		if($result>0){
			return ['info'=>true];
		}
	}
	//批量删除
	function alldel(){
		$allid = input();
		$result = db('cart')->delete($allid['id']);
		if($result>0){
			return ['info'=>true];
		}
	}
	
	function check(){
		$data = input('post.');
		$map['mid'] = session('nameId');
		$arr = ['state'=>0];
		db('cart')->where($map)->update($arr);//初始化购物车状态
		foreach($data['data'] as $k=>$v){
			db('cart')->where(['id'=>$v[0]])->update(['num'=>$v[1],'state'=>1]);
		}
		return ['info'=>true];
	}
	//核对订单
	public function confirm(){
		$mid = session('nameId');
//		print_r($mid);die;
		$list = db('cart')
		->alias('c')
		->field('c.*,p.id as pid,p.name,p.thumb,p.price')
		->join('__PRODUCT__ p','c.pid=p.id','LEFT')
		->where("mid=$mid and state=1")
		->select();
//		print_r($list);die;
		$num=0;
		$total=0;
		foreach($list as $k=>&$v){
			$v['thumb'] = substr($v['thumb'],1);
			$v['total'] = $v['num']*$v['price'];
			$num+=$v['num'];
			$total+=$v['total'];
		}
		$map['mid'] = $mid; 	
		$addrlist = db('address')->where($map)->select();
//		print_r($one);die;
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
			'addrlist'=>$addrlist,
			'num'=>$num,
			'total'=>$total,
			'list'=>$list,
			'clist'=>$clist,
			'title'=>'核对订单'
		]);
		return $this->fetch();
	}
	//订单处理
	function orders(){
		$data = [];
		$data['orderid'] = date('Ymd').session('nameId').mt_rand(10000, 99999);
		$data['mid'] = session('nameId');
		$addr = db('address')->where(['id'=>input('post.addressid')])->find();
		$data['linkman'] = $addr['linkman'];
		$data['mobile'] = $addr['mobile'];
		$data['address'] = $addr['province'].$addr['city'].$addr['country'].$addr['address'];
		$data['state'] = input('post.state');
		$data['status']=0;
		$data['ctime'] = time();
//		print_r($data);die;
		//把所要购买的商品信息查询，商品id,商品价格，商品数量
		$product = db('cart')
		->alias('c')
		->field('c.*,p.price')
		->join('__PRODUCT__ p','c.pid=p.id','LEFT')
		->where(['mid'=>session('nameId'),'state'=>1])
		->select();
//		print_r($product);die;
		$num1 = count($product);
		//开启事务
		Db::startTrans();
		//添加数据到订单表
		$num2=0;
		foreach($product as $k=>$v){
			$data['pid'] = $v['pid'];
			$data['num'] = $v['num'];
			$data['price'] = $v['price']*$v['num'];
			$num2 += db('orders')->insert($data);
		}
		//删除生成订单的购物车商品
		$num3 = db('cart')->where(['mid'=>session('nameId'),'state'=>1])->delete();
		
		if($num1 == $num2 && $num2 == $num3){
			//提交事务
			Db::commit();
			$info = ['info'=>true,'msg'=>'','orderid'=>$data['orderid']];
			return $info; 
		}else{
			//回滚事务
			Db::rollback();
			$info = ['info'=>false,'msg'=>'提交失败','orderid'=>''];
			return $info;
		}
	}
	
	function pay($orderid){
//		$data['state']=input('post.state');
		$orders = db('orders')->where(['orderid'=>$orderid])->select();
//		print_r($orders);die;
		$state = $orders[0]['state'];//支付方式	
		$total=0;
		foreach ($orders as $k => $v) {
			$total +=$v['price'];
		}
		switch ($state){
			case 1:
				//支付宝支付
				alipay($orderid,$total,'测试','描述');
				break;
			case 2:
				//余额支付
				$clist = db('category')->where("fid=2")->select();
				$this->assign([
				'total'=>$total,
				'clist'=>$clist,
				'title'=>'支付密码',
				'orderid'=>$orderid
				]);
			    return $this->fetch();
				break;
		}
	}
	//余额
	function payment(){
		//余额支付处理
		$orderid = input('post.orderid');
//		print_r($orderid);die;
		$total=input('post.total');
		$pwd=input('post.pwd');
		
		$info = ['info'=>false,'msg'=>''];
		//验证支付密码是否正确
		$mid = session('nameId');
//		print_r($mid);die;
		$member = db('money')->where(['mid'=>$mid])->find();
//		print_r($member);die;
		if($member['pwd'] == $pwd){
			//判断余额
			if($member['cash'] < $total){
				$info['code'] = 400;
				$info['msg'] = '余额不足';
				return $info;
			}
			//余额充足
			//开启事务
		    Db::startTrans();
			//用户账号减去支付金额
			
			$result1 = db('money')->where(['mid'=>$mid])->setDec('cash',$total);
			//商家账号减去支付金额
			$result2 = db('money')->where(['mid'=>0])->setInc('cash',$total);
			//修改支付状态
			$result3 = db('orders')->where(['orderid'=>$orderid])->update(['status'=>1]);
//			var_dump($result2);
//			echo $result3;  
//			die;
			if($result1 && $result2 && $result3){
				//提交事务
				Db::commit();
				$info = ['info'=>true,'msg'=>'支付成功'];
				return $info;
			}else{
				//回滚事务
				Db::rollback();
				$info = ['msg'=>'支付失败'];
				return $info;
			}
		}else{
			$info['msg'] = '支付密码错误';
			return $info;
		}
		
	}
	
	function pay_suceess(){
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
		'clist'=>$clist,
		'title'=>'成功'
		]);
		return $this->fetch();
	}
	
	function pay_fail(){
		$clist = db('category')->where("fid=2")->select();
		$this->assign([
		'clist'=>$clist,
		'title'=>'失败'
		]);
		return $this->fetch();
	}
	
	function notify_url(){
		notify_url();
	}
	
	function return_url(){
		return_url();
	}
}
