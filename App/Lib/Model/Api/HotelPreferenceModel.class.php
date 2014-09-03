<?php
class HotelPreferenceModel extends AppBaseMode{




      //获得每日特惠的酒店的id;

	  public function get_id(){
	  
	  
	     return $this->where(  array('is_del'=>0,'time'=>strtotime( date('Y-m-d',time())) )   )->select();
	  
	  
	  }



}