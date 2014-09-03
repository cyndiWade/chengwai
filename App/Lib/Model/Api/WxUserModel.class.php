<?php
class WxUserModel extends ApiBaseModel{


	public function The_existence_of_wxuser($wxuid){

	    return  $this->where(array('wxid'=>"$wxuid"))->find();

	}

	//判断是否有手机号 

	public function The_existence_of_phone($wxuid){
	
	   return $this->where(array('wxid'=>"$wxuid"))->getField('phone');
	}


	//取消关注 
	public function unsubscribe($wxuid){
	
	    $res =  $this->where(array('wxid'=>"$wxuid"))->find();
        
		if(!empty($res)){
			  $data = array(
				  'subscribe'=>2,
				  'subscribe_time'=>time()
			  );
			  $this->where(array('wxid'=>"$wxuid"))->save($data);		
		}
	
	}

	public function get_wx_user($wxuid){
	
	   return $this->where(array('wxid'=>"$wxuid"))->find();
	
	
	}



}