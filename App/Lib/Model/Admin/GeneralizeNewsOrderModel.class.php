<?php
	//新闻媒体推广单表

	class GeneralizeNewsOrderModel extends AdminBaseModel
	{
		
		private $Order_Status;
		
		public function __construct() {
			parent::__construct();
			$this->Order_Status = C('Order_Status');
		}
		
		public function get_order_list ($where) {
			$result = $this->where($where)->order(array('id'=>'ASC'))->select();
			if ($result == true) {
				
				foreach($result as $key=>$val) {
				
					$user_info = D('Users')->get_user_info(array('id'=>$val['users_id']));
					$result[$key]['order_user_account'] = $user_info['account'];
					
					//媒体账号数
					$result[$key]['account_num'] =  D('GeneralizeNewsAccount')->where(array('generalize_id'=>$val['id']))->count();
					
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