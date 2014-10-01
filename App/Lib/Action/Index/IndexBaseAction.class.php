<?php

/**
 * 核心类
 */
class IndexBaseAction extends AppBaseAction {

	
	protected  $is_check_rbac = false;		//当前控制是否需要验证RBAC
	
	protected  $not_check_fn = array();	//登陆后无需登录验证方法
	
	
	//构造方法
	public function __construct() {
		parent:: __construct();			//重写父类构造方法
		
		//全局系统变量
		$this->global_system();
		
		//初始化用户数据
		$this->check_system_info();

	}
	
	
	/**
	 * 全局系统用到的数据
	 */
	private function global_system () {
		
		/* SESSION信息验证保存 */
		$session_userinfo = parent::get_session('user_info');
		if (!empty($session_userinfo)) {
			$this->is_login = true;
			$this->oUser = (object) $session_userinfo;					//转换成对象
		}
		
		$path = preg_replace("/[\\\+]/", "/",dirname($_SERVER['SCRIPT_NAME']));
		//全局模板变量
		parent::global_tpl_view(array(
				'button'=>array(
						'prve'=>C('PREV_URL')
				),
				'path'=>'http://'.$_SERVER['SERVER_NAME'].$path.''.'/Public/'.GROUP_NAME.'/',
				
				'group_name' =>GROUP_NAME,
				
				'module_name'=>MODULE_NAME,
				
				'action_name'=>ACTION_NAME,
				
				//网站当前分组资源路径
				'Group_Resource_Path'=>APP_PATH.'Public/'.GROUP_NAME.'/',
				
				//模块级页面路径
				'Module_Resource_Path'=>APP_PATH.'Public/'.GROUP_NAME.'/Module/'.MODULE_NAME.'/',
		
				'user_info' => array(
					'account' => $this->oUser->account
				),
			));

	}
	
	
	//验证方法
	private function check_system_info () {
		$check_result = $this->init_check($this->oUser);
		if ($check_result['status'] == false) $this->error($check_result['message']);
	}
	
	
	/**
	 * 二级分类URL集合
	 */
	protected function two_urls () {
	
		//头部右上角
		$data[0] = array(
// 			0 => U('/Advert/News/news_list'),
// 			1 => U('/Advert/News/add_generalize'),
// 			2 => U('/Advert/News/generalize_activity'),
		);
		
		//中间导航
		$data[1] = array(
				0 => U('/Index/Index/index'),
				1 => U('/Index/Index/news'),
				2 => U('/Index/Index/wechat'),
				3 => U('/Index/Index/weibo'),
				4 => U('/Index/Index/spokesperson'),
				5 => U('/Index/Index/kh_case'),
				//6 => U('/Index/Index/vip'),
				7 => U('/Index/Index/about_us'),
				8 => U('/Media/Account/login'),		//媒体主登录
				9 => U('/Advert/Account/login'),	//广告主登录
		);
		
		//关于我们导航
		$data[2] = array(
			0 => U('/Index/Index/about_us'),
			//1 => U('/Index/Index/news'),
			2 => U('/Index/Index/kh_case'),
			3 => U('/Index/Index/contact'),
		);

		parent::data_to_view(array(
			//以及导航URL
			'sidebar_one_url'=>	$data[0],//URL
			//二级导航
			'sidebar_two_url'=> $data[1],//URL
			//关于我们左侧导航
			'sidebar_three_url'=> $data[2],//URL
		));
	}
	

	
}


?>