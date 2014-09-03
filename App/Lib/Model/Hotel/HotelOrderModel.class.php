<?php 
class HotelOrderModel extends AppBaseModel{
	
	
	//获得所有的订单
	
	public function get_all_order($user_id){
		$data = $this->field('h.hotel_name,o.*,r.title')
		->table($this->prefix.'hotel_order AS o')
		->join($this->prefix.'hotel_room AS r ON o.hotel_room_id=r.id')
		->join($this->prefix.'hotel AS h ON o.hotel_id = h.id')
		->where(array('o.user_id'=>$user_id,'o.is_del'=>0))
		->select();
		return $data;
	}
	
	public function update_order($id,$arr){
		
		return $this->where(array('id'=>$id))->save($arr);
		
	}
  
	
	
	
	
}


?>