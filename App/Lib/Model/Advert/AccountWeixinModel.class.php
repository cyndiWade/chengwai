<?php
	//微信草根表

	class AccountWeixinModel extends AdvertBaseModel
	{

		//接受参数 返回草根信息数据  用户ID
		public function getPostcgArray($array,$id,$gloubid)
		{
			
			if(!empty($array))
			{
				//过滤数据
				$addvalue = array();
				foreach($array as $key=>$value)
				{
					if($value!='')
					{
						$addvalue[$key] = addslashes($value);
					}
				}
				//组合生成查询SQL
				$where = $this->getcgWhere($addvalue);
				//为草根 传参 0
				$where['w.is_celebrity'] = 0;
				//判断是分页提交还是分栏提交
				$limit = 10;
				//如果有分页参数
				if( $addvalue['p']!='' )
				{
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$returnList = $this->setcgSql($addvalue,$p_limit,$limit,$where,$id,0,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setcgSql($addvalue,0,$limit,$where,$id,0,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setcgSql($addvalue,$now_page,$limit,$where,$id,$is_celebrity,$gloubid)
		{
			//查询出该用户拉黑的名单
			$Blackorcollection = D('BlackorcollectionWeixin');
			if($addvalue['ckhmd']==1)
			{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('IN',$weixinId_array);
				}else{
					return false;
				}
			}else if($addvalue['cksc']==1)
			{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,1);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('IN',$weixinId_array);
				}else{
					return false;
				}
			}else{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('NOT IN',$weixinId_array);
				}
			}
			$where['w.is_del'] = 0;
			$where['w.status'] = 1;
			$count = $this->where($where)
			->table('app_account_weixin as w')
			->join('app_grassroots_weixin as b on w.id = b.weixin_id')
			->count();
			//差集统计长度
			
			//统计表字段，加上别名
			$account_weixin_fields = parent::field_add_prefix('AccountWeixin','bs_','w.');
			$grassroots_weixin_fields = parent::field_add_prefix('GrassrootsWeixin','sy_','b.');
			$Region = D('Region');	//区域表
			
			if($addvalue['ids']!='')
			{
				$where['w.id'] = array('IN',$addvalue['ids']);
				$list = $this->where($where)
				->table('app_account_weixin as w')
				->join('app_grassroots_weixin as b on w.id = b.weixin_id')
				->field($account_weixin_fields.','.$grassroots_weixin_fields)
				->select();
			}else{
				$list = $this->where($where)
				->table('app_account_weixin as w')
				->join('app_grassroots_weixin as b on w.id = b.weixin_id')
				->limit($now_page,$limit)
				->field($account_weixin_fields.','.$grassroots_weixin_fields)
				->select();
			}	
			
			
			$tags_ids = C('Big_Nav_Class_Ids.weixin_caogen_tags_ids');		
			$CategoryTagsInfo = D('CategoryTags')->get_classify_data($tags_ids['top_parent_id']);
			$data['cjfl'] = $CategoryTagsInfo[$tags_ids['cjfl']];
			
			
			
			//排序按照val排序数据
			foreach ($data as $key=>$info) {
				$data[$key] = regroupKey($info,'val',true);
			}
			
				
			if ($list == true) {
				foreach ($list as $key=>$val) {
					//是否收藏
					$list[$key]['pg_sc'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>1,'weixin_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity));
					//是否拉黑
					$list[$key]['pg_lh'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>0,'weixin_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity));

					//地区
					$region_info = $Region->get_regionInfo_by_id($val['sy_cirymedia']);
					$list[$key]['pg_area_name'] = $region_info['region_name'] ? $region_info['region_name'] : '不限';
					
					//价格换算
					$list[$key]['bs_dtb_money'] = $val['bs_dtb_money'] + ($val['bs_dtb_money'] * $gloubid);
					$list[$key]['bs_dtwdyt_money'] = $val['bs_dtwdyt_money'] + ($val['bs_dtwdyt_money'] * $gloubid);
					$list[$key]['bs_dtwdet_money'] = $val['bs_dtwdet_money'] + ($val['bs_dtwdet_money'] * $gloubid);
					$list[$key]['bs_dtwqtwz_money'] = $val['bs_dtwqtwz_money'] + ($val['bs_dtwqtwz_money'] * $gloubid);

					//名人领域
					$cjfl = $data['cjfl'][$val['sy_common']]['title'];
					$list[$key]['pg_cjfl_explain'] = $cjfl ? $cjfl : '不限';
				}
			}	
			
			return array('list'=>$list,'count'=>$count);
		}
		
		//生成不同的WHERE子句
		private function getcgWhere($addslArray)
		{
			$wheres = array();
			if($addslArray['ids']!='')
			{
				$wheres['w.id'] = array('in',explode(',',$addslArray['ids']));
			}
			//处理分类数据 常见分类
			if($addslArray['cjfl']!='')
			{
				$wheres['b.common'] = $addslArray['cjfl'];
			}
			//价格区间
			if($addslArray['zfjg_type']!='' && $addslArray['jg']!='')
			{
				switch ($addslArray['zfjg_type']) {
					case 1:
						$wheres['b.dtb_money'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 2:
						$wheres['b.dtwdyt_money'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 3:
						$wheres['b.dtwdet_money'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 4:
						$wheres['b.dtwqtwz_money'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
				}
			}
			//地方名人/媒体
			if($addslArray['dfmr_mt']!='')
			{
				//$wheres['b.cirymedia'] = $addslArray['dfmr_mt'];
				$wheres['b.cirymedia'] = array('in',explode(',',$addslArray['dfmr_mt']));
			}
			//粉丝数量
			if($addslArray['fans_num']!='')
			{
				$wheres['b.fans_number'] = $this->getLeftRightstr($addslArray['fans_num'],'-');
			}
			//视频认证  粉丝量已认证
			if($addslArray['fs_num_rz']!='')
			{
				$wheres['b.video_fans_on'] = $addslArray['fs_num_rz'];
			}
			//视频认证   受众性别已认证	
			if($addslArray['fs_sex_rz']!='')
			{
				$wheres['b.video_sex_on'] = $addslArray['fs_sex_rz'];
			}
			//账号是否认证
			if($addslArray['zhsfrz']!='')
			{
				$wheres['b.account_on'] = $addslArray['zhsfrz'];
			}
			//受众性别
			if($addslArray['szxb']!='')
			{
				$sex = substr($addslArray['szxb'],0,1);
				$bfb = substr($addslArray['szxb'],1,2);
				if($sex=='x')
				{
					$wheres['b.audience_man'] = array('GT',$bfb);
				}else{
					$wheres['b.audience_women'] = array('GT',$bfb);
				}
			}
			//粉丝量认证时间
			if($addslArray['fsrzsj']!='')
			{
				$wheres['b.fans_c_time'] = array('LT',$addslArray['fsrzsj']);
			}
			//周平均阅读数
			if($addslArray['zpjyds']!='')
			{
				$wheres['b.read_number'] = $this->getLeftRightstr($addslArray['zpjyds'],'-');
			}
			//为您推荐
			if($addslArray['tj']!='')
			{
				$wheres['b.recommend'] = 1;
			}
			//热门微博
			if($addslArray['rmwx']!='')
			{
				$wheres['b.is_hot'] = 1;
			}
			//折扣
			if($addslArray['xstj']!='')
			{
				$wheres['b.specialoffer'] = 1;
			}
			//搜索框的账号名
			$account_name = trim($addslArray['account']);
			if($account_name!='')
			{
				//$wheres['w.account_name'] = array('like','%'.$account_name.'%');
				$wheres['w.account_name|w.weixinhao'] = array('like','%'.$account_name.'%');
			}
			return $wheres;
		}


		//对价格进行分区
		private function getLeftRightstr($string,$needle)
		{
			$str_array = array();
			$num = strpos($string,$needle);
			$left = substr($string,0,$num);
			$right = substr($string,$num+1);
			if($left!='' && $right=='')
			{
				$str_array = array('GT',$left);
			}else if($left=='' && $right!='')
			{
				$str_array = array('LT',$right);
			}else{
				$str_array = array('between',array($left,$right));
			}
			return $str_array;
		}


		//接受参数 返回名人信息数据  | 用户ID
		public function getPostmrArray($array,$id,$gloubid)
		{
			
			if(!empty($array))
			{
				//过滤数据
				$addvalue = array();
				foreach($array as $key=>$value)
				{
					if($value!='')
					{
						$addvalue[$key] = addslashes($value);
					}
				}
				//组合生成查询SQL
				$where = $this->getmrWhere($addvalue);
				//为名人 传参 1
				$where['w.is_celebrity'] = 1;
				//判断是分页提交还是分栏提交
				$limit = 10;
				//如果有分页参数
				if( $addvalue['p']!='' )
				{
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$returnList = $this->setmrSql($addvalue,$p_limit,$limit,$where,$id,1,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setmrSql($addvalue,0,$limit,$where,$id,1,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setmrSql($addvalue,$now_page,$limit,$where,$id,$is_celebrity,$gloubid)
		{
			//查询出该用户拉黑的名单
			$Blackorcollection = D('BlackorcollectionWeixin');
			if($addvalue['ckhmd']==1)
			{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('IN',$weixinId_array);
				}else{
					return false;
				}
			}else if($addvalue['cksc']==1)
			{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,1);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('IN',$weixinId_array);
				}else{
					return false;
				}
			}else{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('NOT IN',$weixinId_array);
				}
			}
			$where['w.is_del'] = 0;
			$where['w.status'] = 1;
			$count = $this->where($where)
			->table('app_account_weixin as w')
			->join('app_celeprityindex_weixin as b on w.id = b.weixin_id')
			->count();
			
			//统计表字段，加上别名
			$account_weixin_fields = parent::field_add_prefix('AccountWeixin','bs_','w.');
			$iceleprityindex_weixin_fields = parent::field_add_prefix('CeleprityindexWeixin','sy_','b.');


			if($addvalue['ids']!='')
			{
				$where['w.id'] = array('IN',$addvalue['ids']);
				$list = $this->where($where)
				->table('app_account_weixin as w')
				->join('app_celeprityindex_weixin as b on w.id = b.weixin_id')
				->field($account_weixin_fields.','.$iceleprityindex_weixin_fields)
				->select();
			}else{
				//差集统计长度
				$list = $this->where($where)
				->table('app_account_weixin as w')
				->join('app_celeprityindex_weixin as b on w.id = b.weixin_id')
				->limit($now_page,$limit)
				->field($account_weixin_fields.','.$iceleprityindex_weixin_fields)
				->select();
			}
				
			//导航用到的标签数据
			$tags_ids = C('Big_Nav_Class_Ids.weixin_celebrity_tags_ids');
			$CategoryTagsInfo = D('CategoryTags')->get_classify_data($tags_ids['top_parent_id']);	
			$data['phd'] = $CategoryTagsInfo[$tags_ids['phd']];	//配合度
			$data['mrzy'] = $CategoryTagsInfo[$tags_ids['mrzy']];	//名人职业
			$data['mtly'] = $CategoryTagsInfo[$tags_ids['mtly']];	//名人领域
			$data['mrxb'] = $CategoryTagsInfo[$tags_ids['mrxb']];	//名人性别

			$Region = D('Region');	//区域表	
			
			//排序按照val排序数据
			foreach ($data as $key=>$info) {
				$data[$key] = regroupKey($info,'val',true);
			}
			
			if($list == true) {
				foreach ($list as $key=>$val) {
					//是否收藏
					$list[$key]['pg_sc'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>1,'weixin_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity));
					//是否拉黑
					$list[$key]['pg_lh'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>0,'weixin_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity));
					
					//参考报价
					$list[$key]['bs_ck_money'] = $val['bs_ck_money'] + ($val['bs_ck_money'] * $gloubid);

					//配合度	
					$phd = $data['phd'][$val['sy_coordination']]['title'];
					$list[$key]['pg_phd_explain'] = $phd ? $phd : '不限';	
					
					//名人置业
					$mrzy = $data['mrzy'][$val['sy_occupation']]['title'];
					$list[$key]['pg_occupation_explain'] = $mrzy ? $mrzy : '不限';
					
					//名人领域
					$mtly = $data['mtly'][$val['sy_field']]['title'];
					$list[$key]['pg_field_explain'] = $mtly ? $mtly : '不限';
					
					//地方名人
					$region_info = $Region->get_regionInfo_by_id($val['sy_cirymedia']);
					$list[$key]['pg_cirymedia_explain'] = $region_info['region_name'] ? $region_info['region_name'] : '不限';
				
					//名人性别
					$mrxb = $data['mrxb'][$val['sy_sex']]['title'];
					$list[$key]['pg_mrxb_explain'] = $mrxb ? $mrxb : '不限';						
					
				}
			}
			
			return array('list'=>$list,'count'=>$count);
		}
		
		//生成不同的WHERE子句
		private function getmrWhere($addslArray)
		{
			$wheres = array();
			if($addslArray['ids']!='')
			{
				$wheres['w.id'] = array('in',explode(',',$addslArray['ids']));
			}
			//名人职业
			if($addslArray['mrzy']!='')
			{
				$wheres['b.occupation'] = $addslArray['mrzy'];
			}
			//媒体领域
			if($addslArray['mtly']!='')
			{
				$wheres['b.field'] = $addslArray['mtly'];
			}
			//价格区间
			if($addslArray['ckbj_type']!='' && $addslArray['jg']!='')
			{
				switch ($addslArray['ckbj_type']) {
					case 1:
						$wheres['b.ck_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 2:
						$wheres['b.yc_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
				}
			}
			//地方名人媒体
			if($addslArray['dfmr_mt']!='')
			{
				//$wheres['b.cirymedia'] = $addslArray['dfmr_mt'];
				$wheres['b.cirymedia'] = array('in',explode(',',$addslArray['dfmr_mt']));
			}
			//兴趣标签
			if($addslArray['xqbq']!='')
			{
				$wheres['b.interest'] = $addslArray['xqbq'];
			}
			//名人/媒体类别
			if($addslArray['mr_mtlb']!='')
			{
				$wheres['b.cmcategoris'] = $addslArray['mr_mtlb'];
			}
			//配合度
			if($addslArray['phd']!='')
			{
				$wheres['b.coordination'] = $addslArray['phd'];
			}
			//粉丝数
			if($addslArray['mr_fans_num']!='')
			{
				$wheres['b.fansnumber'] = $this->getLeftRightstr($addslArray['mr_fans_num'],'-');
			}
			//支持原创
			if($addslArray['zhyc']!='')
			{
				$wheres['b.originality'] = $addslArray['zhyc'];
			}
			//名人性别
			if($addslArray['mrxb']!='')
			{
				$wheres['b.sex'] = $addslArray['mrxb'];
			}
			//战略合作媒体
			if($addslArray['zlhzmr_mt']!='')
			{
				$wheres['b.ctrategy_c'] = 1;
			}
			
			//为您推荐
			if($addslArray['tj']!='')
			{
				$wheres['b.recommend'] = 1;
			}
			//折扣
			if($addslArray['xstj']!='')
			{
				$wheres['b.specialoffer'] = 1;
			}
			
			//搜索框的账号名
			$account_name = trim($addslArray['account']);
			if($account_name!='')
			{
				$wheres['w.account_name'] = array('like','%'.$account_name.'%');
			}
			return $wheres;
		}



		//获得广告位类型
		public function getWeiType($type,$account_id)
		{
			if($account_id!='')
			{
				switch($type)
				{
					//1为单图文 2多图文第一条 3多图文第二条 4多图文第3-N条
					case 1:
						$value = $this->where(array('id'=>$account_id))->field('dtb_money')->find();
						return $value['dtb_money'];
					break;
					case 2:
						$value = $this->where(array('id'=>$account_id))->field('dtwdyt_money')->find();
						return $value['dtwdyt_money'];
					break;
					case 3:
						$value = $this->where(array('id'=>$account_id))->field('dtwdet_money')->find();
						return $value['dtwdet_money'];
					break;
					case 4:
						$value = $this->where(array('id'=>$account_id))->field('dtwqtwz_money')->find();
						return $value['dtwqtwz_money'];
					break;
				}
			}
		}


		//获得微信的价格
		public function getWXMoney($account_id)
		{
			if($account_id!='')
			{
				$value = $this->where(array('id'=>$account_id))->field('ck_money')->find();
				return $value['ck_money'];
			}
		}

		//获得微信号所属的媒体主
		public function getUserId($account_id)
		{
			if($account_id!='')
			{
				$value = $this->where(array('id'=>$account_id))->field('users_id')->find();
				return $value['users_id'];
			}
		}

		//获得微信数据
		public function getInfo($account_id,$is_type)
		{
			$app_account_weixin = parent::field_add_prefix('AccountWeixin','bs_','w.');
			if($is_type==0)
			{
				$app_grassroots_weixin = parent::field_add_prefix('GrassrootsWeixin','sy_','g.');
				$field = $app_account_weixin . ',' . $app_grassroots_weixin;
				return $this->table('app_account_weixin as w')
				->where(array('w.id'=>$account_id))
				->join('app_grassroots_weixin as g on g.weixin_id = w.id')
				->field($field)->find();
			}else{
				$app_grassroots_weixin = parent::field_add_prefix('CeleprityindexWeixin','sy_','c.');
				$field = $app_account_weixin . ',' . $app_grassroots_weixin;
				return $this->table('app_account_weixin as w')
				->where(array('w.id'=>$account_id))
				->join('app_celeprityindex_weixin as c on c.weixin_id = w.id')
				->field($field)->find();
			}
		}
	}