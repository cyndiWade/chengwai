<?php

//酒店模型表
class HotelModel extends AdminBaseModel {
	
	//获取一条酒店数据
	public function get_one_hotel ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	//删除一条数据
	public function del_one_hotel ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	
	/*
	 * 获得酒店的名字
	 */
	public function get_hotel_city(){
		
		
		$data =  $this->where(array('is_del'=>0))->field('hotel_cs')->group('hotel_cs')->select();
		
		return $data;
	}
	//获取一条酒店数据
	public function get_hotel ($condition,$limit ,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
	
		return $this->where($con)->field($field)->limit($limit)->select();
	}
	
    public function get_hotel_name ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
	
		return $this->where($con)->field($field)->select();
	}

	public function save_one_data ($hotel_id) {
		return $this->where(array('id'=>$hotel_id))->save();
	}


	//人工预定获取酒店名字
	public function get_all_hotel_name() {
		return $this->field('id,hotel_name')->where(array('is_del'=>0))->select();
	}

}

?>
