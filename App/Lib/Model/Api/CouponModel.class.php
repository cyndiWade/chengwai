<?php
class CouponModel extends ApiBaseModel{


   public function get_coupon($type){
      

      $where = array('is_del'=>0,'status'=>0,'over_time'=>array('GT',strtotime(date('Y-m-d',time()))),'number'=>array('GT',0),'type'=>$type);
      $data = $this->where($where)->select();
	  $i =1;
	  foreach($data as $key=>$val){
	     if($i>10)break;
		 $type  = $i==1 ? 2 : 1;
         $arr[] = array(
						'Title'=>$val['title'],
						'Description'=>$val['title'],
						'Picurl' =>$this->get_img($val['id'],$type),
						'Url'    =>C('COUPON_URL').$val['id'],
						);
		 $i++;
	  
	  }
	  return $arr;
   
   }

//获取用户的优惠券
   public function get_user_coupon($user_code){
      
       $where = array(
       'uc.wxid'=>"$user_code",
       'c.is_del'=>0,
       'uc.is_del'=>0,
       'status'=>0,
       'over_time'=>array('GT',strtotime(date('Y-m-d',time()))),
       'number'=>array('GT',0)
       );
	   $data = $this->field('*')
		   ->table($this->prefix.'user_coupon AS uc')
		   ->join($this->prefix.'coupon AS c ON uc.coupon_id = c.id')
		   ->where($where)
		   ->select();
	   //return array();
		$i =1;
		foreach($data as $key=>$val){
	     if($i>10)break;
		 $type  = $i==1 ? 2 : 1;
		 $msg_content = $val['msg_content'];
         $arr[] = array(
				'Title'=>$val['msg_content'],//$val['title'],
				'Description'=>$val['title'],
				'Picurl' =>'',//$this->get_img($val['id'],$type),
				'Url'    =>C('COUPON_URL').$val['id'].'/type/1',
		);
		 $i++;
	  
	  }
	  return $arr;

   }

   public function qx_coupon($con){
      
      return  $this->where($con)->save(array('is_del'=>-2));
	  

   }
   public function get_img($coupon_id,$type){
   
       $data = $this->field('i.url,i.coupon_id')
			->table($this->prefix.'coupon AS c')
			->join($this->prefix.'coupon_img  AS i on i.coupon_id = c.id')
			->where(array('i.is_del'=>0,'i.type'=>$type , 'i.coupon_id'=>$coupon_id)  )
			->find();
	   //$arr[$data['coupon_id']] = $data['url'];
	   return C('COUPON_IMG').$data['url'];
   
   }

}