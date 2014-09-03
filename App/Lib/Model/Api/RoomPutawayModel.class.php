<?php
class RoomPutawayModel extends ApiBaseModel{


       // 获得 下架的酒店的房型
	   public function get_room_id(){
	   
	   
	      $arr = $this->where(array('start_time'=>array('ELT',time()),'over_time'=>array('EGT',time())) )->field('hotel_room_id')->select();
		  $id = array();
	      foreach($arr as $key=>$val){
		    
		     $id[] = $val['hotel_room_id'];
		  }
	      return $id;
	   }


}