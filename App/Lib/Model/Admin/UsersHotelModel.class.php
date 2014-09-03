<?php

//账号酒店关系表
class UsersHotelModel extends AdminBaseModel {
	
	//获取账号下酒店
	public function get_user_hotels ($user_id,$field='*') {
		$data = $this->field('h.*')
		->table($this->prefix.'users_hotel AS uh')
		->join($this->prefix.'hotel AS h ON h.id=uh.hotel_id')
		->where(array('uh.user_id'=>$user_id,'uh.is_del'=>0,'h.is_del'=>0))
		->select();
		return $data;
	}
	
	
	//删除酒店关系
	public function del_user_hotel ($user_id,$hotel_id) {
		return $this->delete_data(array('user_id'=>$user_id,'hotel_id'=>$hotel_id));
	}
	
	//获取酒店ID
	public function get_hotel_userid($hotel_id) {
		return $this->where(array('hotel_id'=>$hotel_id))->getField('user_id');
	}
	
}

?>
