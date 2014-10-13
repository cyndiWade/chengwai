<?php

//用户数据模型
class UsersModel extends AdvertBaseModel {
	
	//添加账号
	public function add_account($account,$password) {
		//写入数据库
		$map['account'] = $account;
		$map['password'] = md5($password);
		$map['last_login_time'] = time();
		$map['last_login_ip'] = get_client_ip();
		$map['create_time'] = time();
		$map['update_time'] = time();
		$map['type'] = 2;				//用户类型需要修改
		return $this->add($map);
	}

	//通过账号验证账号是否存在
	public function account_is_have ($account) {
		return $this->where(array('account'=>$account,'type'=>2))->getField('id');
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
	}
	
	public function seek_all_data () {
		$data = $this->field('u.id,u.account,u.last_login_time,u.last_login_ip,u.type,u.status')
		->table($this->prefix.'users AS u')
		->where(array('u.is_del'=>0,'u.type'=>array('neq',0)))
		->select();
		parent::set_all_time($data, array('last_login_time'));
		return $data;
	}
	

	//检测用户密码是否正确
	public function checkUserNew($array,$users_id)
	{
		if($users_id!='')
		{
			if($array['new_pass']==$array['new_passes'])
			{
				$oldPass = $this->where(array('id'=>$users_id))->field('password')->find();
				if($oldPass['password']==md5($array['old_pass']))
				{
					$update = array('password'=>md5($array['new_pass']));
					$bool = $this->where(array('id'=>$users_id))->save($update);
					if($bool)
					{
						return '1';
					}else{
						return '2';
					}
				}else{
					return '3';
				}
			}else{
				return '4';
			}
		}
	}
}

?>
