<?php
class  OrderLogModel extends  AppBaseModel{

    
	
	public  function orderlog($arr){
	
	    $this->user_id  = $arr['user_id'];
		$this->order_id = $arr['order_id'];
		$this->addtime = time();
		$this->msg = $arr['msg'];		
		return $this->add();
	
	}



}