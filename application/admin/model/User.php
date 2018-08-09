<?php
namespace app\admin\model;

use think\Model;

class User extends Model{
	protected $table = 'tp_manager';
	
	// 指定添加时间和修改时间的字段
    protected $createTime = 'ctime';
	//写入时间
	protected $autoWriteTimestamp = true;
}
?>