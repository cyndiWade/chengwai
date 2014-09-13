<?php

/**
 * 广告主账号控制器
 */
class MemberAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array('register','check_login','login','logout');	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '我是广告主';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
		//用户所有的信息都保存在$this->oUser 中，对象方式调用
		$this->_user_id =$this->oUser->id;
		parent::data_to_view(array('account'=>$this->oUser->account));
	}
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'Verify'=>'Verify',
			'User_advertisement' => 'User_advertisement'
	);
	
	
	

	//资料编辑
	public function datum_edit() {
		if($this->isPost())
		{
			$bool = $this->db['User_advertisement']->save_account_list($_POST,$this->_user_id);

			if ($bool == true) {
				parent::callback(C('STATUS_SUCCESS'),'','',array('info'=>'提交成功'));
			} else {
				parent::callback(C('STATUS_UPDATE_DATA'),'','',array('info'=>'请重新尝试下'));
			}
		}

		//选中样式
		$this->data_to_view(array('member_sidebar_datumEdit_class'=>'class="on"',));
		$big_list = $this->db['User_advertisement']->select_account_list($this->_user_id);
		parent::data_to_view($big_list);
		$this->display();
	}
	
	
	//密码修改
    public function pass_save() {
    	
    	//选中样式
    	$this->data_to_view(array('member_sidebar_passSave_class'=>'class="on"'));
    	$this->display();
    }
   
    
    //评价list
    public function evaluate () {
    	
    	//选中样式
    	$this->data_to_view(array(
    			'member_sidebar_evaluate_class'=>'class="on"',
    	));
    	$this->display();
    }
    
}

?>