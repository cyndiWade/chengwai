<?php

//用户与订单关系表
class UsersRelationOrderModel extends AdminBaseModel {
	
	public function add_data($type,$order_id,$user_id) {
		
		if ($type == 1) {
			
		} elseif ($type == 2) {
			
		} elseif ($type == 3) {
			
		} elseif ($type == 4) {
			
		} elseif ($type == 5) {
			
		}
		
		$this->type = $type;
		$this->user_id = $user_id;
		$this->order_id = $order_id;
		return $this->add();
		
	}
	
	public function get_user_detail_info_list ($type) {
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$result = array();
	
		//媒体主
		if ($type == C('ACCOUNT_TYPE.Media')) {
			
			$user_media_fields = parent::field_add_prefix('UserMedia','mt_','m.');	
			$result = $this->field($users_fields.','.$user_media_fields)
			->table($this->prefix.'users AS u')
			->join($this->prefix.'user_media AS m ON u.id = m.users_id')
			->where(array('u.type'=>$type,'u.is_del'=>0))
			->select();

		//广告主	
		} elseif ($type == C('ACCOUNT_TYPE.Advert')) {
			
			$user_advertisement_fields = parent::field_add_prefix('UserAdvertisement','ad_','a.');
			$result = $this->field($users_fields.','.$user_advertisement_fields)
			->table($this->prefix.'users AS u')
			->join($this->prefix.'user_advertisement AS a ON u.id = a.users_id')
			->where(array('u.type'=>$type,'u.is_del'=>0))
			->select();
		}
		
		parent::set_all_time($result, array('bs_last_login_time'));
		
		
		return $result;
	}
	
	
	public function get_user_detail_info_one ($id = 2) {
		$result = array();
		
		$users_fields = parent::field_add_prefix('Users','bs_');
		
		$user_base  = $this->field($users_fields)->where(array('id'=>$id))->find();
		
		//媒体主
		if ($user_base['bs_type'] == C('ACCOUNT_TYPE.Media')) {
			$user_media_fields = parent::field_add_prefix('UserMedia','mt_');
			$user_media_info = D('UserMedia')->field($user_media_fields)->where(array('users_id'=>$id))->find();
		
			$result  = array_merge($user_base,$user_media_info);
		}elseif ($user_base['bs_type'] == C('ACCOUNT_TYPE.Advert')) {
			$user_advertisement_fields = parent::field_add_prefix('UserAdvertisement','ad_');
			$user_advert_info = D('UserAdvertisement')->field($user_advertisement_fields)->where(array('users_id'=>$id))->find();

			$result  = array_merge($user_base,$user_advert_info);
		}
		
		
		
		return $result;
	}
	
	
	
}

?>
