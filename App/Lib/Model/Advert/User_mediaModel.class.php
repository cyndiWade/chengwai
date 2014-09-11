<?php

//用户数据模型
class User_mediaModel extends AdvertBaseModel 
{
	//判断信息是否存在
	public function add_account_list($array)
	{
		if($this->account_is_have($array['users_id'])=='')
		{
			$this->users_id = $array['users_id'];
			$this->iphone = $array['iphone'];
			$this->add();
		}else{
			
		}
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