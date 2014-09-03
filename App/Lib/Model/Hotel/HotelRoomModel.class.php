<?php
class HotelRoomModel extends AppBaseModel{
	
    public function get_room_type ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->select();
	}
	// 获得一条房型信息
	public function get_one_room_type($condition,$field = '*'){
	    $con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->select();
	
	}
	
}