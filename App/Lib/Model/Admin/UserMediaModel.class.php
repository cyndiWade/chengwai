<?php

//媒体主用户信息表
class UserMediaModel extends AdminBaseModel  
{
	
	public function save_info ($users_id,$data) {
		return $this->where(array('users_id'=>$users_id))->save($data);
	}
	
	
}