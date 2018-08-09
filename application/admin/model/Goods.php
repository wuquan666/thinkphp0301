<?php
namespace app\admin\model;
use think\Model;

class Goods extends Model{
	protected $table = 'tp_product';
	// 指定添加时间和修改时间的字段
    protected $createTime = 'ctime';
    protected $updateTime = 'uptime';
	//写入时间
	protected $autoWriteTimestamp = true;
}
?>