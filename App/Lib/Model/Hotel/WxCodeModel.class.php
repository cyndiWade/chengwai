<?php

//二维码模型表
class WxCodeModel extends AppBaseModel {
	

	//获取酒店二维码
	public function seek_hotel_codes ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		
		return $this->where($con)->field($field)->select();
	}
	
	
	//获取一条数据
	public function seek_one_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	
	//修改一条数据
	public function save_one_code ($code_id) {
		return parent::save_one_data(array('id'=>$code_id))	;
	}

	
}

?>
