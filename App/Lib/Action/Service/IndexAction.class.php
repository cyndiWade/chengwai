<?php

/**
 * 媒体主表账号控制器
 */
class IndexAction extends AppBaseAction 
{
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		
	//是否需要RBAC登录验证
	
	//无需登录和验证rbac的方法名
	protected  $not_check_fn = array('register', 'check_login', 'login', 'logout', 'register_accout', 'checkPhone', 'checkUserName','verify');	
	
	//控制器说明
	private $module_explain = '导入数据库';

	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'Verify'=>'Verify',
			'User_media' => 'UserMedia',
			'Blank' => 'Blank'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		//parent::global_tpl_view(array('module_explain'=>$this->module_explain));
	}
	
	//导入会员数据
	public function importUser()
	{
		echo "es";exit;
		$where['userid'] =   array('between','9459, 9549'); //6497 9549
		$memberList = M('member_old')->field('`username`, `password`, `truename`, `mobile`, `money`, `locking`, `regtime`, `loginip`, `logintime`, `logintimes` , 	`company`, `email`, `gender`, `mobile`, `qq`, `credit`, `money`, `locking`')->where($where)->order("userid")->select();
		
		
		foreach ($memberList as $key=>$value)
		{
			//会员基本表
			$arryMember = array();		
			$arryMember['account']			= $value['username'];
			$arryMember['nickname']			= $value['truename'];
			$arryMember['password']			= $value['password'];
			$arryMember['last_login_time']	= $value['logintime'];			
			$arryMember['last_login_ip']	= $value['loginip'];
			$arryMember['login_count']		= $value['logintimes'];			
			$arryMember['create_time']		= $value['regtime'];
			$arryMember['update_time']		= $value['logintime'];
			$arryMember['type']				= 2;
			
			$info = M('users')->add($arryMember);
					
			//广告主表
			$arryAdMember = array();
			$arryAdMember['users_id']			= $info;
			$arryAdMember['company']			= $value['company'];
			$arryAdMember['contact_email']		= $value['email'];
			$arryAdMember['contact_phone']		= $value['mobile'];
			$arryAdMember['contact_qq']			= $value['qq'];
			$arryAdMember['money']				= $value['money'];			
			$arryAdMember['freeze_funds']		= $value['locking'];					
			
			$adInfo = M('user_advertisement')->add($arryAdMember);
			
			echo $key ."   会员表：".$info."     广告主表：".$adInfo."<br/>";
		}
		
	}
}