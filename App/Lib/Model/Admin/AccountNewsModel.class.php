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
		
		//标签查询数据
		$tags_ids = C('Big_Nav_Class_Ids.xinwen_tags_ids');
		$CategoryTagsInfo  = D('CategoryTags')->get_classify_data($tags_ids['top_parent_id']);
		
		$data['hyfl'] = $CategoryTagsInfo[$tags_ids['hyfl']];
		
		//新闻源处理
		$data['sfxwy'] = $CategoryTagsInfo[$tags_ids['sfxwy']];
			
		//文本连接
		$data['dljzk'] = $CategoryTagsInfo[$tags_ids['dljzk']];
	
		//门户类型
		$data['mh_type'] = $CategoryTagsInfo[$tags_ids['mh_type']];
		
		//周末发稿
		$data['press_weekly_select'] = array(
			array('title'=>'是','val'=>1),	
			array('title'=>'否','val'=>0),
		);
		
		//推荐
		$data['recommended_status_select'] = array(
			array('title'=>'是','val'=>1),
			array('title'=>'否','val'=>0),
		);

		//暂不接单
		$data['tmp_receiving_status_select'] = array(
			array('title'=>'是','val'=>1),
			array('title'=>'否','val'=>0),
		);
		
		//是否接单
		$data['receiving_status_select'] = array(
			array('title'=>'是','val'=>1),
			array('title'=>'否','val'=>0),
		);
		
		//限时特价
		$data['specialoffer_select'] = array(
			array('title'=>'是','val'=>1),
			array('title'=>'否','val'=>0),
		);
		
		
		$region_info = D('Region')->get_regionInfo_by_id($result['ac_area_id']);

		$data['pg_area_name'] = $region_info['region_name'] ? $region_info['region_name'] : '不限';
		
		$result = array_merge($result,$data);
	
		return $result;
	}
	
}

?>
