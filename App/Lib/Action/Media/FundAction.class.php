<?php

/**
 * 帐号资金
 */
class FundAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '账单查询';


	//初始化数据库连接
	protected  $db = array(
		'Fund'   => 'Fund',
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
	}
    
    /**
     * 流水明细
     * 
     * @author lurongchang
     * @date   2014-10-13
     * @return void
     */
	public function index()
    {
		import('ORG.Util.Page');
        $page = I('page', 1, 'intval');
        $pageSize = I('pagesize', 20, 'intval');
        
        $userInfos = parent::get_session('user_info');
        $where = array(
            'users_id' => $userInfos['id']
        );
        
        $fundModel = $this->db['Fund'];
        $datas = $fundModel->getList($where, $page, $pageSize);
        
        $Page       = new Page($datas['total'], $pageSize);
		$show       = $Page->show();
        
        parent::data_to_view(array(
            'page' => $show ,
            'list' => $datas['list'],
		));
        
        $this->display();
	}
}