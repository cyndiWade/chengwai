<?php

//微博账号
class AccountWeiboModel extends IndexBaseModel {
	
	public function get_weibo_account_list ($where,$field = '*') {
		$data = $this->field($field)
		->table($this->prefix.'account_weibo AS a')
		->join($this->prefix.'celeprityindex_weibo AS i ON a.id = i.weibo_id')
		->where($where)
		->limit(9)
		->select();
		return $data;
	}
}

?>
