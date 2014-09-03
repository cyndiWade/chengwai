<?php

//酒店房型模型表
class HotelRoomModel extends AdminBaseModel {
	
	//获取酒店下所有房型
	public function get_hotel_rooms ($hotel_id,$field = '*') {
		return $this->field($field)->where(array('hotel_id'=>$hotel_id,'is_del'=>0))->select();
	}
	
	//获取指定一条酒店房型数据
	public function get_one_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	//删除一条数据
	public function del_one_data ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	

	
}

?>
