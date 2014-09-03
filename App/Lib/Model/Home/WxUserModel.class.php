<?php
class WxUserModel extends AppBaseModel{


	public function The_existence_of_wxuser($wxuid){

	    $res =  $this->where(array('wxid'=>"$wxuid"))->find();
        
		if(empty($res)){
			  $data = array(
				  'wxid' =>"$wxuid",
				  'subscribe'=>1,
				  'subscribe_time'=>time()
			  );
			  $this->data($data)->add();		
		}
	
	
	}

	//判断是否有手机号 

	public function The_existence_of_phone($wxuid){
	  
	   $data = $this->where(array('wxid'=>"$wxuid"))->getField('phone');
	   //echo $this->getLastSql();
	   return $data;
	}





}