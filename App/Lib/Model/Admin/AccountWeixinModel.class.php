<?php

//微信账号模型
class AccountWeixinModel extends AdminBaseModel {
	
	
	private $now_table_name = 'AccountWeixin';
	
	public function get_account_count ($where) {
		return $this->where($where)->count();
	}
	
	//all
	public function get_account_data_list ($offst = 0,$limit = 500) {
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$now_base_fields = parent::field_add_prefix('AccountWeixin','ac_','a.');	
		
		$result = array();
		$result = $this->field($now_base_fields.','.$users_fields)
		->table($this->prefix.'account_weixin AS a')
		->join($this->prefix.'users AS u ON a.users_id = u.id')
		->where(array('a.is_del'=>0))
		->limit($offst.','.$limit)
		->select();
		
		if ($result) parent::set_all_time($result,array('ac_create_time'));
		
		return $result;
	}
	

	//一条详细数据
	public function get_account_data_one ($id = 2) {
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$now_base_fields = parent::field_add_prefix('AccountWeixin','ac_','a.');	
		
		//获取基本数据
		$result = array();
		$result = $this->field($now_base_fields.','.$users_fields)
		->table($this->prefix.'account_weixin AS a')
		->join($this->prefix.'users AS u ON a.users_id = u.id')
		->where(array('a.is_del'=>0,'a.id'=>$id))
		->find();
		
		
		//名人数据
		if ($result['ac_is_celebrity'] == 1) {
			$now_mr_sy_fields = parent::field_add_prefix('CeleprityindexWeixin','sy_');
			$Celeprityindex_data = D('CeleprityindexWeixin')->field($now_mr_sy_fields)->where(array('weixin_id'=>$id))->find();
			if (!empty($Celeprityindex_data)) $result = array_merge($result,$Celeprityindex_data);
			
			//微信名人标签数据
			$tags_ids = C('Big_Nav_Class_Ids.weixin_celebrity_tags_ids');
			$CategoryTagsInfo = D('CategoryTags')->get_classify_data($tags_ids['top_parent_id']);
			
			//名人职业
			$data['mrzy'] = $CategoryTagsInfo[$tags_ids['mrzy']];
			
			//名人领域
			$data['mtly'] = $CategoryTagsInfo[$tags_ids['mtly']];
			
			//配合度
			$data['phd_select'] = $CategoryTagsInfo[$tags_ids['phd']];
			
			//是否支持原创 
			$data['zhyc_select'] = $CategoryTagsInfo[$tags_ids['zhyc']];
			
			//名人性别
			$data['sex_select'] = array(
				array('title'=>'不限','val'=>0),
				array('title'=>'男','val'=>1),
				array('title'=>'女','val'=>2),
			);
			
		//草根数据
		} else if ($result['ac_is_celebrity'] == 0) {
			//微信草根索引表
			$now_cg_sy_fields = parent::field_add_prefix('GrassrootsWeixin','sy_');
			$Grassroots_data = D('GrassrootsWeixin')->field($now_cg_sy_fields)->where(array('weixin_id'=>$id))->find();
			if (!empty($Grassroots_data)) $result = array_merge($result,$Grassroots_data);
			
			//微信草根标签
			$tags_ids = C('Big_Nav_Class_Ids.weixin_caogen_tags_ids');
			$CategoryTagsInfo = D('CategoryTags')->get_classify_data($tags_ids['top_parent_id']);
			
			//常见分类
			$data['cjfl'] = $CategoryTagsInfo[$tags_ids['cjfl']];
			
			$data['zhsfrz_select'] = $CategoryTagsInfo[$tags_ids['zhsfrz']];
		}
		
		
		//是否名人
		$data['is_celebrity_select'] = array(
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
		
		
		//地区
		$region_info = D('Region')->get_regionInfo_by_id($result['ac_area_id']);
		$data['pg_area_name'] = $region_info['region_name'] ? $region_info['region_name'] : '不限';
		
		$result = array_merge($result,$data);
	
		
		return $result;
	}
	
	
	
}

?>
