<?php
	//增加数据

	class ExportOrderModel extends AdvertBaseModel
	{
	
		public function get_count ($where) {
			return $this->where($where)->count();
		}
		
		
		public function add_data ($users_id) {
			$this->create();
			$this->users_id = $users_id;
			$this->create_time = time();
			return $this->add();
		}
		
		public function get_list ($where,$limit) {
			$result = $this->where($where)->limit($limit)->select();
			
			$type_explain = array(
				1 => array('name'=>'新闻媒体','url'=>'/Advert/News/news_list'),
				2 => array('name'=>'名人微信','url'=>'/Advert/Weixin/celebrity_weixin'),
				3 => array('name'=>'草根微信','url'=>'/Advert/Weixin/weixin'),
				4 => array('name'=>'名人新浪微博','url'=>'/Advert/Weibo/celebrity_weibo/pt_type/1'),
				5 => array('name'=>'名人腾讯微博','url'=>'/Advert/Weibo/celebrity_weibo/pt_type/2'),
				6 => array('name'=>'草根新浪微博','url'=>'/Advert/Weibo/caogen_weibo/pt_type/1'),
				7 => array('name'=>'草根腾讯微博','url'=>'/Advert/Weibo/caogen_weibo/pt_type/2'),
			);
			
			if (!empty($result)) {
				foreach ($result as $key=>$val) {
					$result[$key]['type_explain'] =$type_explain[$val['type']]['name'];
					$result[$key]['type_url'] =$type_explain[$val['type']]['url'];
				}
			}
			return $result;
		}
	}