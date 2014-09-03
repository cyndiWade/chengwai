<?php
class HotelImgModel extends AppBaseModel{
	
	
   public function get_hotel_images ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->select();
	}
	
   public function get_hotel_image ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	//逻辑删除酒店图片
	public function del_one_image ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	
}