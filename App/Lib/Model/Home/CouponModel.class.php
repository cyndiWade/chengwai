<?php
class CouponModel extends AppBaseModel{




      public function get_coupon($coupon_id){
	  
	     $data = $this->where(array(
			 'is_del'=>0,
			 'status'=>0,
			 'over_time'=>array('GT',strtotime(date('Y-m-d',time()))),
			 'number'=>array('GT',0),
			 'id'=>$coupon_id
			 ))->find();
	    
	     return $data;
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
	   
	   return empty($data['url'])? '' : C('COUPON_IMG').$data['url'];
   
   }

}