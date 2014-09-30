<?php

/**
 * 帮助中心控制器
 */
class HelpAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '帮助中心';


	//初始化数据库连接
	protected $db = array(
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array(
            'module_explain'    => $this->module_explain,
            'module_url'        => U('/Media/Help/index')
        ));
	}
    
    /**
     * 首页
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
	public function index()
    {
        
        
		parent::data_to_view(array(
			'type'          => $type,
            'accountType'   => $accountTypeList,
		));
		$this->display();
	}
}