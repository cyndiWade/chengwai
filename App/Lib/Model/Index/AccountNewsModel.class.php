<?php

//新闻账号
class AccountNewsModel extends IndexBaseModel {
	
	public function get_news_account_list ($where,$field = '*') {
		$data = $this->field($field)
		->table($this->prefix.'account_news AS a')
		->join($this->prefix.'index_news AS i ON a.id = i.news_id')
		->where($where)
		->select();
		return $data;
	}
}

?>
