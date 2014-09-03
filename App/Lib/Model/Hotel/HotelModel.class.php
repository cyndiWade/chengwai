<?php


//酒店模型表
class HotelModel extends AppBaseModel {
	
	//获取一条酒店数据
	public function get_one_hotel ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		
		return $this->where($con)->field($field)->find();
	}
	
	public function get_hotels($condition,$field = '*'){
		
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->select();
		
	}
	
	//删除一条数据
	public function del_one_hotel ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	

	
}

?>
