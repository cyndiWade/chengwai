<?php
class CouponModel extends ApiBaseModel{

	
	public function seek_all_data ($condition,$field) {

		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		$data = $this->where($con)->field($field)->select();
		parent::set_all_time($data, array('start_time','over_time','create_time'),'Y-m-d');
		return $data;
	}

	//获取一条数据
	public function seek_one_coupon($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		$data =  $this->where($con)->field($field)->find();
		parent::set_all_time($data, array('start_time','over_time'),'Y-m-d');
		return $data;
	}
	

}