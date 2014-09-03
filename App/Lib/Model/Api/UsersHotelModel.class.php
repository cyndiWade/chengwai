<?php
class UsersHotelModel extends ApiBaseModel{


      public function get_uid($hotel_id){
	  
	    return  $this->where(array('hotel_id'=>$hotel_id))->getField('user_id');
	  
	  }
}