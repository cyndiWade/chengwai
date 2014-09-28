<?php

//用户数据模型
class UserAdvertisementModel extends AdvertBaseModel 
{
	//判断信息是否存在
	public function add_account_list($array)
	{
		$new_array = array();
		foreach($array as $key => $value)
		{
			$new_array[$key] = addslashes($value);
		}
		$this->add($new_array);
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

	//检车手机号是否存在
	public function iphone_is_have ($iphone) {
		return $this->where(array('contact_phone'=>$iphone))->getField('id');
	}


	//修改用户余额
	public function update_user($array,$users_id)
	{
		$where = array('users_id'=>$users_id);
		$value = $this->where($where)->Field('money')->find();
		$update['money'] = (float)$value['money'] + (float)$array['total_fee'];
		$bool = $this->where($where)->save($update);
		return $bool;
	}

	//获得用户余额
	public function getMoney($id)
	{
		$where = array('users_id'=>$id);
		$value = $this->where($where)->Field('money,freeze_funds')->find();
		return array('money'=>$value['money'],'freeze_funds'=>$value['freeze_funds']);
	}
}