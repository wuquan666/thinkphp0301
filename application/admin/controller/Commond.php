<?php
namespace app\admin\controller;
use think\Controller;

class Commond extends Controller{
	public function __construct(){
		parent::__construct();
		$this->isLogin();
		$menu = $this->menu();
		$this->assign('menu',$menu);
		$controller = strtolower(request()->controller());//获取当前的控制器
		$action = strtolower(request()->action());//获取当前的方法
		$this->assign('controller',$controller);
		$this->assign('action',$action);
		
		$adminlevel = session('level');
		$this->assign('adminlevel',$adminlevel);
		if(session('userId') != config('superadminid')) $this->islevel();
	}
	private function isLogin(){
//		echo '判断是否登陆';die;
		if(!session('?user')){
			$this->error('请先登陆','Admin/login');
		}
	}
	
	function cate($id=0,$list=[],$spac=0){
		if($id>0) $spac ++;
		$fid=$id;
	//	$csql="select * from category where fid=$fid";
		$parentlist = db('category')->where("fid=$fid")->select();
		foreach($parentlist as $k=>$v){
			$v['name']='<font color="red">'.str_repeat('|--',$spac).'</font>'.$v['name'];
			$list[] = $v;
			$list =  $this->cate($v['id'],$list,$spac);
		}
		return $list;
	}
	
	function level($id=0,$list=[],$spac=0){
		if($id>0) $spac ++;
		$fid=$id;
	//	$csql="select * from category where fid=$fid";
		$parentlist = db('level')->where("fid=$fid")->select();
		foreach($parentlist as $k=>$v){
			$v['name']='<font color="red">'.str_repeat('|--',$spac).'</font>'.$v['name'];
			$list[] = $v;
			$list =  $this->level($v['id'],$list,$spac);
		}
		return $list;
	}
	
	//后台分页按钮代码块
	function page($table,$p,$number){
		
	
		$module=Request()->module();
		// 调用Request对象的controller方法
		$controller=Request()->controller();
		// 调用Request对象的action方法
		$action=Request()->action();
		// 调用Request对象的ext方法
		
		$surl="http://www.tp0301.com/$module/$controller/$action";
//		print_r($surl);die;
		//总页码
		$parentlist = db('level')->select();
//		print_r($parentlist);die;
		
		//总条数
		$count = count($parentlist);
		$totalpage=ceil($count/$number);
		
		$prev=($p-1)<=0?1:($p-1);
		$page='<a href="'.$surl.'?p='.$prev.'" title="上一页">上一页</a>';
		$page .='<a href="'.$surl.'?p=1" title="首页">首页</a>';
		
		$start=$p-2;
		$end=$p+2;
		
		if($totalpage<5){
			$start=1;
			$end=$totalpage;
		}
		//print_r($totalpage);die;
		if($start<1){
			$start=1;
			$end=$start+4;
		}
		
		if($end>$totalpage){
			$end=$totalpage;
			$start=$end-4;
		}
		
		for($i=$start;$i<=$end;$i++){
			if($p==$i){
				$page .='<a href="'.$surl.'?p='.$i.'" class="current" title="1">'.$i.'</a>';
			}else{
				$page .='<a href="'.$surl.'?p='.$i.'" title="1">'.$i.'</a>';
			}
		
		}
		
		$next=($p+1)>$totalpage?$totalpage:($p+1);
		$page .='<a href="'.$surl.'?p='.$next.'"  title="下一页">下一页</a>';
		$page .='<a href="'.$surl.'?p='.$totalpage.'" title="尾页">尾页</a>';
		
		return $page;
	}
	//上传
	function upload($files){
		$file = request()->file($files);
		$dataImg = [];
		if($file){
		foreach($file as $k=>$v){
				$filePath = './public/static/admin/upload/'.date('Y-m-d');
				//echo $filePath;die;
				if(!file_exists($filePath)){
					mkdir($filePath,0777,true);
				}
				//验证格式，并上传
				 $info = $v->validate(['size'=>2*1024*1024,'ext'=>"jpg,png,gif,jpeg"])->rule('uniqid')->move($filePath);
				 if($info){
				 	$dataImg[] = rtrim($filePath,'/').'/'.$info->getSaveName();
				}
			}	 
		}
		return $dataImg;
	}
	//缩略图
	function thumb($path,$width=150,$height=150){
		$image = \think\Image::open($path);
		//按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
		$tPath = dirname($path).'/thumb';
		if(!file_exists($tPath)){
			mkdir($tPath,0777,true);
		}
		//./public/static/admin/upload/2018-07-08/thumb
		$thumbPath = $tPath.'/'.basename($path);
		//./public/static/admin/upload/2018-07-08/thumb_5b3eca95c6862.jpg
		$image->thumb($width,$height)->save($thumbPath);
		return $thumbPath;
	}
	//水印图
	function water($path,$type=2,$state=9){
		$image = \think\Image::open($path);
		//给原图的左上角添加水印并保存water_image.png
		if($type == 1){
			switch($state){
				case 1:
					$image->water('./logo.png',\think\Image::WATER_NORTHWEST)->save($path);
					break;
				case 2:
					$image->water('./logo.png',\think\Image::WATER_NORTH)->save($path);
					break;
				case 3:
					$image->water('./logo.png',\think\Image::WATER_NORTHEAST)->save($path);
					break;
				case 4:
					$image->water('./logo.png',\think\Image::WATER_WEST)->save($path);
					break;
				case 5:
					$image->water('./logo.png',\think\Image::WATER_CENTER)->save($path);
					break;
				case 6:
					$image->water('./logo.png',\think\Image::WATER_EAST)->save($path);
					break;
				case 7:
					$image->water('./logo.png',\think\Image::WATER_SOUTHWEST)->save($path);
					break;
				case 8:
					$image->water('./logo.png',\think\Image::WATER_SOUTH)->save($path);
					break;
				case 9:
					$image->water('./logo.png',\think\Image::WATER_SOUTHEAST)->save($path);
					break;
			}
		}else{
			$image->text('黑店','./public/static/admin/font/msyh.ttf',20,'#ffffff')->save($path);
		}	
	}

	//权限限制
	function menu($id=0,$list=[],$spac=0,$state=1){
		if($id>0) $spac ++;
		$fid=$id;
	//	$csql="select * from category where fid=$fid";
		$parentlist = db('level')->where("fid=$fid and state=1")->select();
		foreach($parentlist as $k=>$v){
			$list[] = $v;
			$list =  $this->menu($v['id'],$list,$spac);
		}
		return $list;
	}
	
	function islevel(){
		$controller = strtolower(request()->controller());//获取当前的控制器
		$action = strtolower(request()->action());//获取当前的方法
		$one = db('level')->where("controller='$controller' and action='$action'")->find();
		if(!in_array($one['id'],session('level'))){
			$this->error('无权访问','admin/logout');
		}
	}
}	
?>