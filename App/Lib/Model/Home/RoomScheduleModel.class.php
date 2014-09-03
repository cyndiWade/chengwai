<?php

//房型价格模型表
class RoomScheduleModel extends HomeBaseModel {
	
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
		$data =  $this->field('id,hotel_room_id,day,spot_payment,prepay')->where(array('hotel_room_id'=>$hotel_room_id,'is_del'=>0))->order('id ASC')->select();
		parent::set_all_time($data, array('day'),'Y-m-d');
		return $data;
	}
	
	public function get_all_data ($condition) {
		$con = array('is_del'=>0,'day'=>strtotime(date('Y-m-d',time())));
		
		array_add_to($con,$condition);
		
		return $this->where($con)->select();
		

	} 
	//判断房间数量是否足够
   public function room_num_enough($data){

		$where= array(
			'hotel_room_id'=>$data['room_id'],
		    //'room_num'=>min('room_num'),
			'day' =>array(
				array('egt',strtotime($data['checkinday']) ),
				array('elt',strtotime($data['checkoutday']) )
		    ),
		);
     
		$arr = $this->where($where)->having('room_num <'.$data['house'])->select();
	
		return $arr;
		
	}
	//订单完成后对应的当天 对应的酒店房型 -对应的房间数量
	public function Update_Room_num($where,$room_num){
		
		 $data = $this->where($where)->select();

		 foreach($data as $key=>$val){
		 	
		 	$arr = array(
		 		'room_num'=>$val['room_num']-$room_num
		 	);
		 	
		 	$this->where(array('id'=>$val['id']))
		 	->save($arr);
		 	
		 }
		 
		
	}
	
	//根据酒店放房型的id获得酒店
	public function get_hotel($room_id){
		
		$data = $this->field('h.hotel_name')
		->table($this->prefix.'hotel AS h')
		->join($this->prefix.'hotel_room AS hr ON h.id=hr.hotel_id')
		->where(array('h.is_del'=>0,'hr.is_del'=>0,'hr.id'=>$room_id))
		->find();
		return $data;
		
	}
	
	
}

?>
