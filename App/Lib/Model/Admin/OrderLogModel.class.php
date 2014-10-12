<?php
	//订单日志表

	class OrderLogModel extends AdminBaseModel
	{
		
		//插入订单日志
		public function add_order_log ($user_id,$order_id,$type) {
			$content = $this->content;
			
			switch ($type) {
				case 1:	//新闻推广单
					$account_datas = D('GeneralizeNewsAccount')->where(array('generalize_id'=>$order_id))->select();
					break;
				case 2:	//微博推广单
					$account_datas = D('GeneralizeAccount')->where(array('generalize_id'=>$order_id))->select();
					break;
				case 3:	//微博意向单
					$account_datas = D('IntentionWeiboAccount')->where(array('intention_id'=>$order_id))->select();
					break;
				case 4:	//微信推广单
					$account_datas = D('GeneralizeWeixinAccount')->where(array('generalize_id'=>$order_id))->select();
					break;
				case 5:	//微信意向单
					$account_datas = D('IntentionWeixinAccount')->where(array('generalize_id'=>$order_id))->select();
					break;			
			}
			
			
			//批量添加日志
			foreach ($account_datas as $key=>$val) {
				$this->user_id = $user_id;
				$this->order_id = $order_id;
				$this->type = $type;
				$this->account_id = $val['account_id'];
				$this->content = $content;
				$this->create_time = time();
				$status = $this->add();
			}
			
			return $status;
			//dump($this->oUser)
		}
		
		
		//获取订单日志列表
		public function get_order_list ($where) {//)->order('score desc')->
			$result = $this->where($where)->order('id DESC')->select();
			if ($result == true) {
				parent::set_all_time($result,array('create_time'));
				return $result;
			} else {
				return array();
			}
		}
		
		
		
		
	}