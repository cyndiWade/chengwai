<?php
class WxCodeModel extends ApiBaseModel{

    function get_hotel_user_id($code_id){
	
	 return $this->where(array('code_id'=>$code_id))->find();
	
	}
   

  
}