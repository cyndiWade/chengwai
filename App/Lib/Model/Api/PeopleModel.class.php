<?php

//优惠模型表
class PeopleModel extends ApiBaseModel {
	
	public function seek_all_data ($condition,$field = '*') {
		$con = array('p.is_del'=>0);
		array_add_to($con,$condition);
		$data = $this->field('p.*,c.title')
		->table($this->prefix.'people AS p')
		->join($this->prefix.'coupon AS c ON c.id=p.coupon_id')
		->where($con)
		->select();
		parent::set_all_time($data, array('create_time'));
		return $data;
	}
	
	
	public function get_one_people($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		return $this->field($field)->where($con)->find();
	}
	
	
	public function add_one_people() {
		$this->create_time = time();
		return $this->add();
	}
	
	//删除一条数据
	public function del_one_data ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	

	
}

?>
