<?php

//微信账号
class AccountWeixinModel extends IndexBaseModel {
	
	public function get_weixin_account_list ($where,$field = '*') {
		$data = $this->field($field)
		->table($this->prefix.'account_weixin AS a')
		->join($this->prefix.'celeprityindex_weixin AS i ON a.id = i.weixin_id')
		->where($where)
		->select();
		
		return $data;
	}
}

?>
