<?php

//房型下架模型表
class RoomPutawayModel extends AppBaseModel {
	
	//获取指定的数据
	public function seek_one_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		
		$data = $this->where($con)->field($field)->find();
		
		parent::set_all_time($data, array('start_time','over_time'),'Y-m-d');
		parent::set_all_time($data, array('create_time'));
		return $data;
	}
	
	//获取指定所有数据
	public function seek_all_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
	
		$data = $this->where($con)->field($field)->select();
	
		parent::set_all_time($data, array('start_time','over_time'),'Y-m-d');
		parent::set_all_time($data, array('create_time'));
		return $data;
	}
	
	
	//添加一条数据
	public function add_one_data () {
		$this->start_time = strtotime($this->start_time);
		$this->over_time = strtotime($this->over_time);
		$this->create_time = time();
		return $this->add();
		
	}
	
	//删除
	public function del_one_data ($room_putaway_id) {
		return $this->where(array('id'=>$room_putaway_id))->data(array('is_del'=>-2))->save();
	}
	
	//修改
	public function save_data ($room_putaway_id) {
		$this->start_time = strtotime($this->start_time);
		$this->over_time = strtotime($this->over_time);
		return $this->where(array('id'=>$room_putaway_id))->save();
	}

}

?>
