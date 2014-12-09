<?php
/**
 * 新闻媒体账号
 */
class MoneyAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '资金管理';
	
	//初始化数据库连接
	protected  $db = array(
		'Fund'=>'Fund',
		'Users'=>'Users',
		'UserAdvertisement'=>'UserAdvertisement',
		'UserMedia'=>'UserMedia'
	);
	
	private $user_id;
	private $adormed;

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	
		$this->user_id = $this->_get('user_id');
		$this->adormed = $this->_get('adormed');
	}
	
	//流水订单
	public function record () {
		
		if (empty($this->user_id)) $this->error('非法操作');
		
		import('ORG.Util.Page');
		$fund = $this->db['Fund'];
		$where['users_id'] =  $this->user_id;
		$where['adormed'] = $this->adormed;
		$count      = $fund->where($where)->count();
		//$Page       = new Page($count,10);
		//$show       = $Page->show();
		$list = $fund->where($where)->order('id desc')
		->field('*')->select();
		
		parent::global_tpl_view( array(
				'action_name'=>'流水列表',
				'title_name'=>'流水列表',
		));
		parent::data_to_view(array(
				//'page' => $show,
				'list' => $list
		));
		$this->display();
	}
	
	
	//资金调节
	public function money_change () {
		$data = $this->db['Users']->get_account(array('id'=>$this->user_id));
		if (empty($data)) $this->error('此用户不存在！');
		
		if ($this->isPost()) {
			$change = $this->_post('change');
			$money = $this->_post('money');
			$remark = $this->_post('remark');
			
			//添加资金
			if ($change == 1) {  
				$cha_str = '+';
				$shop_number = 'ZJ';
				$type = 7;
				
			} elseif ($change == 0) {
				$cha_str = '-';
				$shop_number = 'KC';
				$type = 8;
			}
			
			
			$add_data['users_id'] = $this->user_id;
			$add_data['money'] = $money;
			$add_data['adormed'] = $this->adormed;
			$add_data['shop_number'] = $shop_number;		
			$add_data['type'] = $type;
			$add_data['member_info'] = recordType($type);
			$add_data['admin_info']  = $remark;
			
			
			$save_data['money'] = array('exp','money'.$cha_str.$money);
			
			//媒体主
			if ($this->adormed == 1) {
					
				
				$db_state = $this->db['UserMedia']->save_info($this->user_id,$save_data);
				
			//广告主
			} elseif ($this->adormed == 2) {

				
				$db_state = $this->db['UserAdvertisement']->save_info($this->user_id,$save_data);
				
			}
			
			
			if ($db_state == true) {
				$this->db['Fund']->add_log($add_data);
				$this->success('操作成功');
			} else {
				$this->error('操作失败');
			}
			exit;
		
			
		}
		
		parent::global_tpl_view( array(
				'action_name'=>'资金变动',
				'title_name'=>'资金变动',
		));
		parent::data_to_view($data);
		$this->display();
	}
	
	
	//媒体主提现
	public function withdraw_deposit_list () {
		$Users = $this->db['Users'];
		$user_status = C('ACCOUNT_STATUS');		//状态
		$user_list = $Users->get_user_detail_info_list(1);

		
		foreach ($user_list AS $key=>$val) {
			$user_list[$key]['status_explain'] = $user_status[$val['bs_status']]['explain'];
		}
		
		
		parent::global_tpl_view( array(
				'action_name'=>'提现',
				'title_name'=>'数据列表',
		));
		
		$this->data_to_view(array('user_list'=>$user_list));	
		$this->display();
	}
	
	
	public function withdraw_deposit () {
		$data = $this->db['Users']->get_account(array('id'=>$this->user_id));
		if (empty($data)) $this->error('此用户不存在！');
			
		if ($this->isPost()) {
			$change = $this->_post('change');
			$money = $this->_post('money');
			$remark = $this->_post('remark');
				
			$cha_str = '-';
			$shop_number = 'TX';
			$type = 9;
				
			$add_data['users_id'] = $this->user_id;
			$add_data['money'] = $money;
			$add_data['adormed'] = $this->adormed;
			$add_data['shop_number'] = $shop_number;
			$add_data['type'] = $type;
			$add_data['member_info'] = recordType($type);
			$add_data['admin_info']  = $remark;
					
			$save_data['money'] = array('exp','money'.$cha_str.$money);
			$db_state = $this->db['UserMedia']->save_info($this->user_id,$save_data);
		
			if ($db_state == true) {
				$this->db['Fund']->add_log($add_data);
				$this->success('操作成功');
			} else {
				$this->error('操作失败');
			}
			exit;		
		}
		
		
		
		parent::global_tpl_view( array(
				'action_name'=>'资金变动',
				'title_name'=>'提现',
		));
		parent::data_to_view($data);
		$this->display();
		
	}
	
	
	

}