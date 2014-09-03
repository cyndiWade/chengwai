<?php
class HotelRoomModel extends ApiBaseModel{



      public function get_room_type($room,$hotel_id,$pay_type){
         $where =array(
	         'hr.hotel_id'=>$hotel_id,
	         'title'=>array('like',"%$room%"),
	         'hr.is_del'=>0,
	         'rs.day'=>strtotime( date('Y-m-d',time())) 
         );
	     $data =  $this->field('hr.id as room_id ,hr.*,rs.*')
			->table($this->prefix.'hotel_room AS hr')
			->join($this->prefix.'room_schedule AS rs on rs.hotel_room_id = hr.id')
			->where( $where )
			->find();
	    $data['price'] = $pay_type==1 ? $data['spot_payment'] : $data['prepay'];
		$data['pay_type'] = $pay_type;
	    return $data;
	  }
	  
// 计算价格
	  public function total_price($hotel_room_id,$starttime,$endtime,$type){
	  	
	  	$filed = $type == 1 ? 'spot_payment' : 'prepay';
	  	$where = array(
		  	's.is_del'=>0,
		  	's.day' =>array(
				array('egt',strtotime($starttime) ),
				array('lt',strtotime($endtime) )
		    ),
	  	    's.hotel_room_id'=>$hotel_room_id,
		  	
	  	);
	  	
	  	$data = $this->field('*')
	  			->table($this->prefix.'room_schedule as s')
	  			->where($where)
	  			//->select();
	  			->sum($filed);
	  	return $data;
	  
	  	
	  }


}