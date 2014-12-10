<?php

//标签类别表
class AccountNewsModel extends AdminBaseModel {
	
	private $now_table_name = 'AccountNews';
	
	//all
	public function get_account_data_list () {
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$now_base_fields = parent::field_add_prefix('AccountNews','ac_','a.');
	
		$result = array();
		$result = $this->field($now_base_fields.','.$users_fields)
		->table($this->prefix.'account_news AS a')
		->join($this->prefix.'users AS u ON a.users_id = u.id')
		->where(array('a.is_del'=>0))
		->select();
	
		if ($result) {
			//foreach ($result as $key=>$val) {
				//$result[$key]['pg_account_num'] = $this->where(array('users_id'=>$val['bs_id']));
			//}
			parent::set_all_time($result,array('ac_create_time'));
		}
	
		return $result;
	}
	
	
	//一条详细数据
	public function get_account_data_one ($id = 2) {
	
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$now_base_fields = parent::field_add_prefix('AccountNews','ac_','a.');
	
		$result = array();
		$result = $this->field($now_base_fields.','.$users_fields)
		->table($this->prefix.'account_news AS a')
		->join($this->prefix.'users AS u ON a.users_id = u.id')
		->where(array('a.is_del'=>0,'a.id'=>$id))
		->find();
	
		return $result;
	}
	
}

?>
