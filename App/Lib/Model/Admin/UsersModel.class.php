<?php

//用户数据模型
class UsersModel extends AdminBaseModel {
	
	public function get_account_count ($where) {
		return $this->where($where)->count();
	}
	
	//添加账号
	public function add_account($type) {
		//写入数据库
		$this->password = pass_encryption($this->password);
		$time = time();
		$this->last_login_time = $time;
		$this->last_login_ip = get_client_ip();
		$this->create_time = $time;
		$this->update_time = $time;
		$this->type = $type;				//用户类型
		return $this->add();
	}
	
	
	//通过账号验证账号是否存在
	public function account_is_have ($account) {

		return $this->where(array('account'=>$account))->getField('id');
	}
	
	//获取账号数据
	public function get_user_info ($condition) {
		return $this->where($condition)->find();
	}
	
	public function get_account ($condition) {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);
		return $this->where($con)->field('account')->find();
	}
	
	//修改密码
	public function modifi_user_password ($id,$password) {
		return $this->where(array('id'=>$id))->save(array('password'=>$password));
	}
	
	
	//更新登录信息
	public function up_login_info ($uid) {
		
		$time = time();
		$con['last_login_time'] = $time;
		$con['last_login_ip'] = get_client_ip();
		$con['login_count'] = array('exp','login_count+1');
		return $this->where(array('id'=>$uid))->save($con);

// 		$time = time();
// 		$this->last_login_time = $time;
// 		$this->last_login_ip = get_client_ip();
// 		$this->login_count = $this->login_count + 1; 
			
// 		$this->where(array('id'=>$uid))->save();
	
	}
	
	
	public function seek_all_data () {
		$data = $this->field('u.id,u.account,u.last_login_time,u.last_login_ip,u.type,u.status')
		->table($this->prefix.'users AS u')
		//->where(array('u.is_del'=>0,'u.type'=>array('neq',0)))
		->where(array('u.is_del'=>0,'u.type'=>array('eq',0)))
		->select();
		parent::set_all_time($data, array('last_login_time'));
		return $data;
	}

	
	public function get_user_detail_info_list ($type,$offset=0,$limit=500) {
		$users_fields = parent::field_add_prefix('Users','bs_','u.');
		$result = array();
		
		//媒体主
		if ($type == C('ACCOUNT_TYPE.Media')) {
			
			$user_media_fields = parent::field_add_prefix('UserMedia','mt_','m.');	
			$result = $this->field($users_fields.','.$user_media_fields)
			->table($this->prefix.'users AS u')
			->join($this->prefix.'user_media AS m ON u.id = m.users_id')
			->where(array('u.type'=>$type,'u.is_del'=>0))
			->limit($offset.','.$limit)
			->select();

		//广告主	
		} elseif ($type == C('ACCOUNT_TYPE.Advert')) {
			
			$user_advertisement_fields = parent::field_add_prefix('UserAdvertisement','ad_','a.');
			$result = $this->field($users_fields.','.$user_advertisement_fields)
			->table($this->prefix.'users AS u')
			->join($this->prefix.'user_advertisement AS a ON u.id = a.users_id')
			->where(array('u.type'=>$type,'u.is_del'=>0))
			->limit($offset.','.$limit)
			->select();
		}
	
		/*//统计订单总数  
		foreach ($result as $key=>$val) {
			$result[$key]['ac_news_num'] = D('AccountNews')->where(array('users_id'=>$val['bs_id']))->count();
			$result[$key]['ac_weibo_num'] = D('AccountWeibo')->where(array('users_id'=>$val['bs_id']))->count();
			$result[$key]['ac_weixin_num'] = D('AccountWeixin')->where(array('users_id'=>$val['bs_id']))->count();
			$result[$key]['pg_all_account_num'] = $result[$key]['ac_news_num'] + $result[$key]['ac_weibo_num'] + $result[$key]['ac_weixin_num'];
		}*/
		
		
		parent::set_all_time($result, array('bs_last_login_time'));
		parent::set_all_time($result, array('bs_create_time'));
		
		return $result;
	}
	
	
	public function get_user_detail_info_one ($id = 2) {
		$user_status = C('ACCOUNT_STATUS');		//状态
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
		
		if (!empty($result)) {
			$result['pg_status_explain'] = $user_status[$result['bs_status']]['explain'];
		}
		
		
		return $result;
	}
	
	
	
}

?>
