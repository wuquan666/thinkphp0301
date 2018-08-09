<?php
namespace app\admin\validate;
use think\Validate;

class Brand extends Validate{
	protected $rule= [
		'name' => 'require|max:18',
	];
}
?>