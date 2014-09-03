<?php
//账号酒店关系表
class UsersHotelModel extends AppBaseModel {
	
	//获取账号下酒店
	public function get_user_hotels ($user_id,$field='*') {
		$data = $this->field('h.*')
		->table($this->prefix.'users_hotel AS uh')
		->join($this->prefix.'hotel AS h ON h.id=uh.hotel_id')
		->where(array('uh.user_id'=>$user_id,'uh.is_del'=>0))
		->select();
		return $data;
	}
	
}