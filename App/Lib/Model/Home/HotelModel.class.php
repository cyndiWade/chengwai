<?php
class HotelModel extends HomeBaseModel{
	
	
    //获取一条酒店数据
	public function get_one_hotel ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	public function get_hotels($condition,$field = '*'){
		
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
        

		return $this->where($con)->field($field)->order('sort DESC')->select();

//		$data = $this->where($con)->field($field)->select();
//		foreach ($data as $key=>$val){
//            $data[$key]['img'] = $this->get_img($val['id'],2);//array('hotel_id'=>$hotel_id )
//			$price = $this->get_price($val['id']);
//			$data[$key]['spot_payment'] = $price['spot_payment'];
//			$data[$key]['prepay'] = $price['prepay'];
//
//		}
//		return $data;

		
	}
	public function get_all_data($hotel_cs){
	
	    $data = $this->field('*')
		->table($this->prefix.'hotel AS h')
		->join($this->prefix.'hotel_room AS hr ON h.id=hr.hotel_id')
		->join($this->prefix.'room_schedule AS rs on rs.hotel_room_id = hr.id')
		->where(array('hotel_cs'=>$hotel_cs,'h.is_del'=>0))
		->select();
		return $data;
	
	
	}


   //获得图片
	public function get_img($hotel_id,$type){
	  
	        

			 $data = $this->table($this->prefix.'hotel AS h')
			->join($this->prefix.'hotel_img AS hr ON h.id=hr.hotel_id')
			->where( array('hr.hotel_id'=>$hotel_id,'hr.is_del'=>0,'hr.type'=>$type) )
			->getField('url');

			 return C('PUBLIC_VISIT.domain').C('UPLOAD_DIR.image').$data;
	  
	  
	  }

	  public function get_price($hotel_id){

		  $data = $this->field('h.id,h.hotel_name , h.hotel_syq, h.hotel_pf ,rs.spot_payment,rs.prepay')
			->table($this->prefix.'hotel AS h')
			->join($this->prefix.'hotel_room AS hr ON h.id=hr.hotel_id')
			->join($this->prefix.'room_schedule AS rs on rs.hotel_room_id = hr.id')
			->where(array('hr.hotel_id'=>$hotel_id,'h.is_del'=>0,'rs.day'=>strtotime( date('Y-m-d',time())) )  )
			->find();

		  return $data;
	  
	  }
  /*
   * 新加 获得酒店房型的图片
   */
      //获得酒店的房型
	  public function get_hotel_room($hotel_id,$type){

	  
	  	$where = array(
		  	'r.hotel_id'=>$hotel_id, //酒店的id
		  	'r.is_del'=>0,      //房型是否删除
		  	's.is_del'=>0,       //房型的价格是否删除
	  	    's.room_num' =>array('gt',0), //房间数量大于0
	  	    's.day'=>strtotime( date( 'Y-m-d',time() ) ), // 今天

	  	);
	  	
	  	$data = $this->field('r.id as rid ,r.title,r.info,s.spot_payment,s.prepay,s.room_num,s.id as sid') 
	  	->table($this->prefix.'hotel_room AS r')
	  	->join($this->prefix.'room_schedule AS s on s.hotel_room_id = r.id')
	  	->where($where)->select();
	  	
	  	foreach($data as $key=>$val){
	  		
	  		$data[$key]['url'] = $this->get_room_img($val['rid'],$type);
	  	}
	  	return $data;
	  	
	  }
	  
	  //根据客人选择的日期获得价格
	  /*public function get_date_room_info($hotel_id,$type,$date){
	  
	  	 
	  	$where = array(
	  			'r.hotel_id'=>$hotel_id, //酒店的id
	  			'r.is_del'=>0,      //房型是否删除
	  			's.is_del'=>0,       //房型的价格是否删除
	  			's.room_num' =>array('gt',0), //房间数量大于0
	  			's.day'=>strtotime( $date ), // 今天
	  
	  	);
	  
	  	$data = $this->field('r.id as rid ,r.title,r.info,s.spot_payment,s.prepay,s.room_num,s.id as sid')
	  	->table($this->prefix.'hotel_room AS r')
	  	->join($this->prefix.'room_schedule AS s on s.hotel_room_id = r.id')
	  	->where($where)->select();
	  
	  	foreach($data as $key=>$val){
	  	  
	  		$data[$key]['url'] = $this->get_room_img($val['rid'],$type);
	  	}
	  	return $data;
	  
	  }*/
	  //获得图片
	  public function get_room_img($room_id,$type){	  	
	  	$where = array(
		  	'i.is_del'=>0,
		  	'i.hotel_room_id' =>$room_id,
	  	    'i.type'  =>$type
	  	);	  	
	  	$data = $this->table($this->prefix.'room_img as i')
	  	->where($where)->getField('url');	
	  	$img = empty($data) ? C('NO_PIC') : $data;  	
	  	return C('PUBLIC_VISIT.domain').C('UPLOAD_DIR.image').$img;
	  	
	  }
	  
	  // 获得酒店
	  public function get_hotel($condition){
	  	$con = array('is_del'=>0);
		array_add_to($con,$condition);
        
        
		$data = $this->where($con)->field($field)->order('sort DESC')->select();
		
		foreach($data as $key=>$val){
			$data[$key]['img'] = $this->get_img($val['id'],4); // 酒店的图片
			$data[$key]['price'] = $this->get_room_price($val['id']);
		}
		//echo $this->getLastSql();
		return $data;
	  	
	  }
	  
	  // 获得酒店房型的价格
	  public function get_room_price($hotel_id){
	  	 
	  	$data = $this->field('rs.spot_payment,rs.prepay')
			->table($this->prefix.'hotel_room AS r')
			->join($this->prefix.'room_schedule AS rs on rs.hotel_room_id = r.id')
			->where(array('r.hotel_id'=>$hotel_id,'r.is_del'=>0,'rs.day'=>strtotime( date('Y-m-d',time())) )  )
			->find();

		  return $data;
	  	
	  }
	 //获得每个酒店取消订单的规则
	 public function Get_order_cancellation_rules($hotel_id){
	 	if(empty($hotel_id)) return '';
	 	return $this->where(array('id'=>$hotel_id))->getField('Order_cancellation_rules');
	 }
	
}