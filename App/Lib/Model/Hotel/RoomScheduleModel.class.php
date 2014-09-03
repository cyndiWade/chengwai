<?php

//房型价格模型表
class RoomScheduleModel extends AppBaseModel {
	
	//查找一条数据
	public function seek_one_schedule ($hotel_room_id,$day) {
		$data = $this->where(array('hotel_room_id'=>$hotel_room_id,'day'=>$day))->find();
		return $data;
	}
	
	
	//编辑价格日历。有则修改，无则添加
	public function seek_update_data ($condition) {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		$id = $this->where($con)->getField('id');
		
		if ($id == true) {
			return $this->where(array('id'=>$id))->save();
		} else {
			return $this->add();
		}
	} 
	
	
	/* 获取指定记录 */
	public function Seek_All_Schedule ($hotel_room_id) {
		$data =  $this->field('id,hotel_room_id,room_num,day,spot_payment,prepay')->where(array('hotel_room_id'=>$hotel_room_id,'is_del'=>0))->order('id ASC')->select();
		parent::set_all_time($data, array('day'),'Y-m-d');
		return $data;
	}
	
	
}

?>
