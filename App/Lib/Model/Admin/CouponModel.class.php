<?php

//优惠模型表
class CouponModel extends AdminBaseModel {
	
	public function seek_all_data ($con,$field) {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		$data = $this->where($con)->field($field)->select();
		parent::set_all_time($data, array('start_time','over_time','create_time'));
		return $data;
	}
	
	//添加一条数据
	public function add_one_coupon () {
		$this->start_time = strtotime($this->start_time);
		$this->over_time = strtotime($this->over_time);
		$this->create_time = time();
		return $this->add();
	}
	
	public function save_one_coupon ($id) {
		$this->start_time = strtotime($this->start_time);
		$this->over_time = strtotime($this->over_time);
		return $this->where(array('id'=>$id))->save();
	}
	
	
	//获取一条数据
	public function seek_one_coupon($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		$data =  $this->where($con)->field($field)->find();
		parent::set_all_time($data, array('start_time','over_time'),'Y-m-d');
		return $data;
	}
	
	
	//删除一条数据
	public function del_one_data ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	

	
}

?>
