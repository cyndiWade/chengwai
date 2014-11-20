<?php

/**
 * 充值页面
 */
class MoneyAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $big_type = 6;
	
	
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
		
		if (ACTION_NAME == 'record') $this->big_type = 4;
		parent::big_type_urls($this->big_type);		//大分类URL

	}
	
	//充值首页
	public function index () {
		//充值订单 充值+UNIX前5位+用户ID+后五位
		$spnumber = 'CZ'.time();
		$money = $this->db['UserAdvertisement']->getMoney($this->oUser->id);
		parent::data_to_view(array(
				'spnumber'=>$spnumber,
				'spye' => $money['money'],
				'spdjzj' => $money['freeze_funds'],
				'all_price' => $money['money'] + $money['freeze_funds'],
				'spshow' => base64_encode('http://'.$_SERVER['HTTP_HOST'].'/index.php/Advert/Money/index.html')
		));
		$this->display();
	}
	
	
	//流水订单
	public function record () {
		import('ORG.Util.Page');
		$fund = $this->db['Fund'];
		$where['users_id'] =  $this->oUser->id;
		$where['adormed'] = 2;
		$count      = $fund->where($where)->count();
		$Page       = new Page($count,10);
		$show       = $Page->show();
		$list = $fund->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')
		->field('shop_number,money,time,member_info,status')->select();
		parent::data_to_view(array(
			'page' => $show,
			'list' => $list
		));
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
			// $tmp_arr = array(
			// 			'id' =>$user_id['id'],
			// 			'account' => $user_id['account'],
			// 			'nickname' => $user_id['nickname'],
			// 			'type'=> $user_id['type'],
			// 		);
			// parent::set_session(array('user_info'=>$tmp_arr));
			//修改流水
			$value = $this->db['Fund']->instertFund($_GET,1);
			//修改用户信息表
			$bool = $this->db['UserAdvertisement']->update_user($_GET,$user_id['id']);
			if($bool)
			{
				parent::updateMoney($user_id['id']);
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