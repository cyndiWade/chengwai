<?php

//用户数据模型
class User_advertisementModel extends AdvertBaseModel 
{
	//判断信息是否存在
	public function add_account_list($array)
	{
		if($this->account_is_have($array['users_id'])=='')
		{
			$this->add($array);
		}
	}
	
	//修改用户信息
	public function save_account_list($array,$id)
	{
		$new_array = array();
		foreach($array as $key => $value)
		{
			$new_array[$key] = addslashes($value);
		}
		return $this->where(array('users_id'=>$id))->save($new_array);
	}

	//查询用户信息
	public function select_account_list($id)
	{
		$list  = $this->where(array('users_id'=>$id))->find();
		$new_array = array();
		foreach($list as $key => $value)
		{
			$new_array[$key] = str_ireplace('\\', '', $value);
		}
		return $new_array;
	}

	//通过账号验证账号是否存在
	private function account_is_have($id) 
	{
		return $this->where(array('users_id'=>$id))->getField('id');
	}

	//检车手机号是否存在
	public function iphone_is_have ($iphone) {
		return $this->where(array('iphone'=>$iphone))->getField('id');
	}



}