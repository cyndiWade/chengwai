<?php

//优惠图片模型表
class CouponImgModel extends ApiBaseModel {
	
	
	
	//获取酒店图片
	public function get_images ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->order('id ASC')->limit('1')->select();
		
	}
	
	//逻辑删除酒店图片
	public function del_one_image ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	
}

?>
