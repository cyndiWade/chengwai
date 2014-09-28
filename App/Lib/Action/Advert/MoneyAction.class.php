<?php

/**
 * 充值页面
 */
class MoneyAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $big_type = 4;
	
	
	private $module_explain = '充值页面页面';
	
	//初始化数据库连接
	protected  $db = array(
		'Fund' => 'Fund',
		'UserAdvertisement' => 'UserAdvertisement'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->_init_data();
	}
	
	
	private function _init_data () {
		parent::global_tpl_view(array(
				'module_explain'=>$this->module_explain,
		));
		
		parent::big_type_urls($this->big_type);		//大分类URL

	}
	
	//充值首页
	public function index () {
		//充值订单 充值+UNIX前5位+用户ID+后五位
		$spnumber = 'CZ'.time();
		parent::data_to_view(array(
				'spnumber'=>$spnumber,
				'spshow' => base64_encode('http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Advert/Money/index.html')
		));
		$this->display();
	}
	
	
	//流水订单
	public function record () {
		$this->display();
	}

	public function goAlipay()
	{
		$new_array = array();
		foreach($_POST as $key=>$value)
		{
			$new_array[$key] = addslashes($value);
		}
		//写入未完成订单到数据库
		$bool = $this->db['Fund']->insertNoArr($new_array,$this->oUser->id,1);
		if($bool)
		{	
			$url = 'http://'.$_SERVER['HTTP_HOST'].'/Alipay/alipayapi.php?spnumber='.$new_array['spnumber'].'&spname='.$new_array['spname'].'&spinfo='.$new_array['spinfo'].'&spprice='.$new_array['spprice'].'&spshow='.$new_array['spshow'];
			header('Location:'.$url);exit;
		}else{
			parent::callback(C('STATUS_NOT_DATA'),'写入数据失败，请稍后重试!');exit;
		}
	}

	public function okAlpay()
	{
		$user_id = $this->db['Fund']->getUserId($_GET);
		if($user_id!='')
		{
			$tmp_arr = array(
						'id' =>$user_id['id'],
						'account' => $user_id['account'],
						'nickname' => $user_id['nickname'],
						'type'=> $user_id['type'],
					);
			parent::set_session(array('user_info'=>$tmp_arr));
			//修改流水
			$value = $this->db['Fund']->instertFund($_GET,1);
			//修改用户信息表
			$bool = $this->db['UserAdvertisement']->update_user($_GET,$user_id['id']);
			if($bool)
			{
				$this->redirect('Advert/Money/index');
			}else{
				parent::callback(C('STATUS_UPDATE_DATA'),'充值失败，请与管理员联系!');
			}
		}else{
			parent::callback(C('STATUS_NOT_DATA'),'非法操作，请稍后重试!');exit;
		}
	}

}

?>