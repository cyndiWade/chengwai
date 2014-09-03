<?php
class HotelOrderModel extends HomeBaseModel{
	
	public function done_add($data){
		
		$this->data($data)->add();
		//echo $this->getLastSql();
		return $this->getLastInsID();
	}
	
	
	public function order_info($condition){
		
		$con = array('o.is_del'=>0);
		array_add_to($con,$condition);
    	$data = $this->field('o.*,h.*,r.*')
		->table($this->prefix.'hotel_order AS o')
		->join($this->prefix.'hotel AS h on o.hotel_id = h.id')
		->join($this->prefix.'hotel_room AS r on r.id = o.hotel_room_id')
		->where($con)
		->find();
        //echo $this->getLastSql();
		return $data;
		
	}


	//È¡Ïû¶©µ¥
	public function quxiao_dingdan($order_id){
	
	        $arr = array('order_status'=>3);
	        $this->where(array('id'=>$order_id))->save($arr);
	
	
	}
}