<?php
class UserCouponModel extends AppBaseModel{



	  public function get_coupon_id ($user_code){
	  
	    $data =  $this->where(array('wxid'=>"$user_code",'is_del'=>0))->select();
		//echo $this->getLastSql();
        foreach($data as $key=>$val){
		    $coupon_id[] = $val['coupon_id'];
		}
		return $coupon_id;
	  }

	//Ìí¼ÓÓÅ»ÝÈ¯
	  public function add_user_coupon($user_code,$coupon_id) {
		$data = $this->where(array('wxid'=>$user_code,'coupon_id'=>$coupon_id))->find();
		if ($data == true) {
			return 0;
		} else {
			return $this->data(array('wxid'=>$user_code,'coupon_id'=>$coupon_id))->add();
		}
	  }

	  //
	  public function qx_coupon($con){
      
         return  $this->where($con)->save(array('is_del'=>-2));
	  

     }




}
