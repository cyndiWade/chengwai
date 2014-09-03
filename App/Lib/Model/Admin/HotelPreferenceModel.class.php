<?php

//每日特惠表
class HotelPreferenceModel extends AdminBaseModel {
	
	//添加酒店每日特惠
	public function add_hotel_preference ($hotel_id) {
		$time = strtotime(date('Y-m-d',time()));
		$data = $this->where(array('hotel_id'=>$hotel_id))->getField('id');
		if ($data == true) {
			return 0;
		} else {
			$this->time = $time;
			$this->hotel_id = $hotel_id;
			return $this->add();
		}
		
	}

	

	
}

?>
