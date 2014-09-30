<?php

/**
 * 活动订单控制器
 */
class EventOrderAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '活动订单';


	//初始化数据库连接
	protected  $db = array(
			// 'CategoryTags'=>'CategoryTags',
			// 'Users' => 'Users',
			// 'Verify'=>'Verify',
			// 'User_media' => 'User_media'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
	}
    
    /**
     * 活动订单首页(待执行订单)
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function index()
    {
		/* parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单' => array('select' => true, 'url' => U('/Media/EventOrder/index')),
				'全部订单'   => array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
			)
		)); */
		$this->display('standbys');
	}
    
    /**
     * 所有订单
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function allorder()
    {
        $this->display('standbys');
	}
	
}