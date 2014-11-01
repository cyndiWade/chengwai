<?php
	//微信推广单表

	class GeneralizeWeixinOrderModel extends AdminBaseModel
	{
		private $Order_Status;
		
		public function __construct() {
			parent::__construct();
			$this->Order_Status = C('Order_Status');
		}
		
		
		public function get_order_list ($where) {
			$result = parent::get_spe_data($where);
			if ($result == true) {
				
				foreach ($result as $key=>$val) {
					//用户
					$user_info = D('Users')->get_user_info(array('id'=>$val['users_id']));
					$result[$key]['order_user_account'] = $user_info['account'];
			
					//媒体账号数
					$result[$key]['account_num'] =  D('GeneralizeWeixinAccount')->where(array('generalize_id'=>$val['id']))->count();
			
					//状态
					$result[$key]['status_explain'] = $this->Order_Status[$val['status']]['explain'];
			
				}
						
					
				parent::set_all_time($result,array('start_time','create_time'));
				return $result;
			} else {
				return array();
			}
		}
		
	}