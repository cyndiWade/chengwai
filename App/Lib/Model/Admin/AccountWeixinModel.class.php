<?php

//微信账号模型
class AccountWeixinModel extends AdminBaseModel {
	
	
	private $now_table_name = 'AccountWeixin';
	
	//all
	public function get_account_data_list () {
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$now_base_fields = parent::field_add_prefix('AccountWeixin','ac_','a.');	
		
		$result = array();
		$result = $this->field($now_base_fields.','.$users_fields)
		->table($this->prefix.'account_weixin AS a')
		->join($this->prefix.'users AS u ON a.users_id = u.id')
		->where(array('a.is_del'=>0))
		->select();
		
		if ($result) parent::set_all_time($result,array('ac_create_time'));
		
		return $result;
	}
	

	//一条详细数据
	public function get_account_data_one ($id = 2) {
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$now_base_fields = parent::field_add_prefix('AccountWeixin','ac_','a.');	
		
		$result = array();
		$result = $this->field($now_base_fields.','.$users_fields)
		->table($this->prefix.'account_weixin AS a')
		->join($this->prefix.'users AS u ON a.users_id = u.id')
		->where(array('a.is_del'=>0,'a.id'=>$id))
		->find();
		
		return $result;
	}
	
	
	
}

?>
