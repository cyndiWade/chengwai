<?php

/**
 * 预约订单控制器
 */
class PlaceAnOrderAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '预约订单';


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
     * 预约订单首页(待执行订单)
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function index()
    {
		parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单' => array('select' => true, 'url' => U('/Media/PlaceAnOrder/index')),
				'全部订单'   => array('select' => false, 'url' => U('/Media/PlaceAnOrder/allorder')),
			),
            'secondPosition' => '待执行订单',
		));
		$this->display();
	}
    
    /**
     * 全部订单
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function allorder()
    {
		parent::data_to_view(array(
            'secondPosition' => '全部订单',
		));
		$this->display();
	}
    
    /**
     * 搜索订单列表
     * 
     * @author lurongchang
     * @date   2014-09-22
     * @return void
     */
	public function searchList()
    {
		if ($this->isPost()){
            $expiredStartTime   = I('expiredStartTime', 0, 'strtotime');
            $expiredEndTime     = I('expiredEndTime', 0, 'strtotime');
            $executionStartTime = I('executionStartTime', 0, 'strtotime');
            $executionEndTime   = I('executionEndTime', 0, 'strtotime');
            $accountName        = I('account_name', '', 'setString');
            $requirementName    = I('requirement_name', '', 'setString');
            $requirementStatus  = I('requirement_status', 0, 'intval');
            
            $userInfos = parent::get_session('user_info');
            $where['users_id'] = &$userInfos['id'];
            
            // $where['']
        } else {
            parent::callback(0, '访问方式错误');
        }
	}
	
}