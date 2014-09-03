<?php
class UsersHotelModel extends  HomeBaseModel{
	
	
	 public function get_user_id($condition){
	 	
	 	$con = array('is_del'=>0);
		array_add_to($con,$condition);
		
	 	 $data = $this->where($con)->field('user_id')->find();
	 	 return $data['user_id'];
	 	
	 }
}