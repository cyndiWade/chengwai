<?php
class WxUserModel extends AppBaseModel{
	
	// 获得用户
	
	public  function get_wx_user($user_id){
	
	  $data=  $this->where(array('user_id'=>$user_id))->select();
	
	  //echo $this->getLastSql();
	  return $data;
	
	}
	
	public function get_all_wx_user($user_id){

	    $data = $this->field('u.uid,u.uname,u.phone,u.subscribe_time,c.yuangong,c.hotel_remarks')
	    ->table($this->prefix.'wx_user as u')
	    ->join($this->prefix.'wx_code as c on u.code_id = c.code_id')
	    ->where(array('u.user_id'=>$user_id))
	    ->select();
	    parent::set_all_time($data, array('subscribe_time'));
		return $data;
	}
	
	/*public function admin_get_wx_user(){
	   
	    $data = $this->field('u.*,c.hotel_name')
	    ->table($this->prefix.'wx_user as u')
		->join($this->prefix.'hotel as c on u.hotel_id = c.id')
	    ->select();
	    parent::set_all_time($data, array('subscribe_time'));
		return $data;
	}
	*/
}