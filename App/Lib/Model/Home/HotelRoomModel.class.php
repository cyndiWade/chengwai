<?php

//酒店房型模型表
class HotelRoomModel extends HomeBaseModel {
	
	//获取酒店下所有房型
	public function get_hotel_rooms ($hotel_id,$field = '*') {
		return $this->field($field)->where(array('hotel_id'=>$hotel_id,'is_del'=>0))->select();
	}
	
	//获取指定一条酒店房型数据
	public function get_one_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
    public function get_all_rooms ($condition,$field = '*') {
    	$con = array('is_del'=>0);
		array_add_to($con,$condition);
		return $this->field($field)->where($con)->select();
	}
	
    public function get_price_room ($condition,$field = '*') {
    	

    	$con = array('hr.is_del'=>0,'rs.is_del'=>0,'rs.day'=>strtotime(date('Y-m-d',time())));
		array_add_to($con,$condition);
    	$data = $this->field('*')
		->table($this->prefix.'hotel_room AS hr')
		->join($this->prefix.'room_schedule AS rs on rs.hotel_room_id = hr.id')
		->where($con)
		->select();

        foreach ($data as $key=>$val){
            $data[$key]['img'] = $this->get_img($condition['hotel_id'],2);//array('hotel_id'=>$hotel_id )
		}
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

	

	
}

?>
