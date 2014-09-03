<?php

//房型价格模型表
class RoomScheduleModel extends AdminBaseModel {
	
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
		$is_cut_off_id  = $this->room_putaway();
		print_R($is_cut_off_id);
		$data =  $this->field('id,hotel_room_id,day,spot_payment,prepay,room_num')->where(array('hotel_room_id'=>$hotel_room_id,'is_del'=>0))->order('id ASC')->select();
		foreach($data as $k=>$v){
			
			$is_cut_off_id = $this->room_putaway($v['hotel_room_id'],$v['day']);
			if($is_cut_off_id){
			   $data[$k]['is_cut_off'] = 1; // 下架了
			}else {
			   $data[$k]['is_cut_off'] = 0; // 没有下架
			} 
		}
		parent::set_all_time($data, array('day'),'Y-m-d');
		
		return $data;
	}
	
	/*根据酒店订单中的信息来获取每日的价格
	 * 
	 */
	
	public function Seek_Data_Schedule($data){
		$where= array(
				'hotel_room_id'=>$data['hotel_room_id'],

				'day' =>array(
						array('egt',$data['in_date'] ),
						array('lt',$data['out_date'] )
				),
		);
		
		$data = $this->where($where)->select();
		parent::set_all_time($data, array('day'),'Y-m-d');

		return $data;
		
	}
	
	
	
//获得下架的房型
	  
	  public function room_putaway($hotel_room_id,$daytime){
	  	$where = array(
		  	'p.is_del'=>0,
		  	'p.start_time'=>array('elt',$daytime ),
		  	'p.over_time'=>array('egt',$daytime ),
	  	    'p.hotel_room_id'=>$hotel_room_id,
	  	);
	  	
	  	$data = $this->field('p.hotel_room_id')
	  	->table($this->prefix.'room_putaway as p')
	  	->where($where)->select();
	  	
	  /*	foreach($data as $key=>$val){
	  		$arr[] = $val['hotel_room_id'];
	  	}*/
	  	return $data;  // 去除重复的值
	  }
	
	
}

?>
