<?php

class IndexAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//控制是否需要RBAC登录验证
	
	protected  $not_check_fn = array('register');	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_name = '主页';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	//初始化数据库连接
	protected  $db = array(
		'CategoryTags'=>'CategoryTags',
	);
	
	public function index () {

		//连接数据库(所有的数据库的链接都这么写，要在相应的Model里面写入方法)
		$CategoryTags= $this->db['CategoryTags'];	

		//把变量注入到view->所有全局注入的标量放在这个方法里里面，比如用户的信息、页面的全局信息			
		parent::global_tpl_view( array(
			'action_name'=>'数据列表',
			'title_name'=>'数据列表',
			'add_name'=>'添加类别'	
		));

		//把变量注入到view->页面的数据，比如列表，字段的赋值、以及只有本页面才会调用的标量都放在这里
		$data['a'] = 123;
		parent::data_to_view($data);

		$this->display();
	}



}

?>