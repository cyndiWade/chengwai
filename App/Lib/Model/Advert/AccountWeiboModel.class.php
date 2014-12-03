<?php


	class AccountWeiboModel extends AdvertBaseModel
	{

		//接受参数 返回草根信息数据 | $type区分新浪 1 或者腾讯 2  | 用户ID
		public function getPostcgArray($array,$type,$id,$gloubid)
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
				//草根表 微博人类型 1 是新浪 2是腾讯
				$where['w.pt_type'] = $type;
				//为草根 传参 0
				$where['w.is_celebrity'] = 0;
				//判断是分页提交还是分栏提交
				$limit = 10;
				//如果有分页参数
				if( $addvalue['p']!='' )
				{
					//总数
					//$count = $addvalue['count'];
					//分页 p
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$returnList = $this->setcgSql($addvalue,$p_limit,$limit,$where,$type,$id,0,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setcgSql($addvalue,0,$limit,$where,$type,$id,0,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setcgSql($addvalue,$now_page,$limit,$where,$type,$id,$is_celebrity,$gloubid)
		{
			//查询出该用户拉黑的名单
			$Blackorcollection = D('BlackorcollectionWeibo');
			if($addvalue['ckhmd']==1)
			{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.id'] = array('IN',$weiboId_array);
				}else{
					return false;
				}
			}else if($addvalue['cksc']==1)
			{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,1);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.id'] = array('IN',$weiboId_array);
				}else{
					return false;
				}
			}else{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.id'] = array('NOT IN',$weiboId_array);
				}
			}
			$where['w.is_del'] = 0;
			$where['w.status'] = 1;
			$count = $this->where($where)
			->table('app_account_weibo as w')
			->join('app_grassroots_weibo as b on w.id = b.weibo_id')
			->count();
			//差集统计长度
			
			//统计表字段，加上别名
			$account_weibo_fields = parent::field_add_prefix('AccountWeibo','bs_','w.');
			$grassroots_weibo_fields = parent::field_add_prefix('GrassrootsWeibo','sy_','b.');
			$Region = D('Region');	//区域表

			if($addvalue['ids']!='')
			{
				$where['w.id'] = array('IN',$addvalue['ids']);
				$list = $this->where($where)
				->table('app_account_weibo as w')
				->join('app_grassroots_weibo as b on w.id = b.weibo_id')
				->field($account_weibo_fields.','.$grassroots_weibo_fields)
				->select();
			}else{
				$list = $this->where($where)
				->table('app_account_weibo as w')
				->join('app_grassroots_weibo as b on w.id = b.weibo_id')
				->limit($now_page,$limit)
				->field($account_weibo_fields.','.$grassroots_weibo_fields)
				->select();
			}
			
			$tags_ids = C('Big_Nav_Class_Ids.caogen_tags_ids');
			$CategoryTagsInfo = D('CategoryTags')->get_classify_data($tags_ids['top_parent_id']);
			$data['cjfl'] = $CategoryTagsInfo[$tags_ids['cjfl']];
			
			
			//重新计算价格
			if($list==true)
			{
				foreach($list as $key=>$val)
				{
					//是否收藏
					$list[$key]['pg_sc'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>1,'weibo_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity,'pt_type'=>$type));
					//是否拉黑
					$list[$key]['pg_lh'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>0,'weibo_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity,'pt_type'=>$type));

					$list[$key]['bs_yg_zhuanfa'] = $val['bs_yg_zhuanfa'] + ($val['bs_yg_zhuanfa'] * $gloubid);
					$list[$key]['bs_yg_zhifa'] = $val['bs_yg_zhifa'] + ($val['bs_yg_zhifa'] * $gloubid);
					$list[$key]['bs_rg_zhuanfa'] = $val['bs_rg_zhuanfa'] + ($val['bs_rg_zhuanfa'] * $gloubid);
					$list[$key]['bs_rg_zhifa'] = $val['bs_rg_zhifa'] + ($val['bs_rg_zhifa'] * $gloubid);
				
					//地区
					$region_info = $Region->get_regionInfo_by_id($val['sy_cirymedia']);
					$list[$key]['pg_area_name'] = $region_info['region_name'] ? $region_info['region_name'] : '不限';	
					
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
			//粉丝数量
			if($addslArray['fans_num']!='')
			{
				$wheres['b.fans_num'] = $this->getLeftRightstr($addslArray['fans_num'],'-');
			}
			//价格区间
			if($addslArray['zfjg_type']!='' && $addslArray['jg']!='')
			{
				switch ($addslArray['zfjg_type']) {
					case 1:
						$wheres['b.yg_zhuanfa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 2:
						$wheres['b.yg_zhifa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 3:
						$wheres['b.rg_zhuanfa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 4:
						$wheres['b.rg_zhifa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
				}
			}
			//地方名人/媒体
			if($addslArray['dfmr_mt']!='')
			{
				//$wheres['b.cirymedia'] = $addslArray['dfmr_mt'];
				$wheres['b.cirymedia'] = array('in',explode(',',$addslArray['dfmr_mt']));
			}
			//性别区分
			if($addslArray['fans_sex']!='')
			{
				$wheres['b.sex'] = $addslArray['fans_sex'];
			}
			//为您推荐
			if($addslArray['tj']!='')
			{
				$wheres['b.recommend'] = 1;
			}
			//热门微博
			if($addslArray['rmwb']!='')
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
				$wheres['w.account_name'] = array('like','%'.$account_name.'%');
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



		//接受参数 返回名人信息数据 | $type区分新浪 1 或者腾讯 2  | 用户ID
		public function getPostmrArray($array,$type,$id,$gloubid)
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
				//草根表 微博人类型 1 是新浪 2是腾讯
				$where['w.pt_type'] = $type;
				//为名人 传参 1
				$where['w.is_celebrity'] = 1;
				//判断是分页提交还是分栏提交
				$limit = 10;
				//如果有分页参数
				if( $addvalue['p']!='' )
				{
					//总数
					//$count = $addvalue['count'];
					//分页 p
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$returnList = $this->setmrSql($addvalue,$p_limit,$limit,$where,$type,$id,1,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setmrSql($addvalue,0,$limit,$where,$type,$id,1,$gloubid);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setmrSql($addvalue,$now_page,$limit,$where,$type,$id,$is_celebrity,$gloubid)
		{
			//0是黑名单 1是收藏
			//查询出该用户拉黑的名单
			$Blackorcollection = D('BlackorcollectionWeibo');
			if($addvalue['ckhmd']==1)
			{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.id'] = array('IN',$weiboId_array);
				}else{
					return false;
				}
			}else if($addvalue['cksc']==1)
			{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,1);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.id'] = array('IN',$weiboId_array);
				}else{
					return false;
				}
			}else{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.id'] = array('NOT IN',$weiboId_array);
				}
			}
			$where['w.is_del'] = 0;
			$where['w.status'] = 1;
			$count = $this->where($where)
			->table('app_account_weibo as w')
			->join('app_celeprityindex_weibo as b on w.id = b.weibo_id')
			->count();
			//差集统计长度
			
			//统计表字段，加上别名
			$account_weibo_fields = parent::field_add_prefix('AccountWeibo','bs_','w.');
			$celeprityindex_weibo_fields = parent::field_add_prefix('CeleprityindexWeibo','sy_','b.');
			$Region = D('Region');	//区域表
			

			if($addvalue['ids']!='')
			{
				$where['w.id'] = array('IN',$addvalue['ids']);
				$list = $this->where($where)
				->table('app_account_weibo as w')
				->join('app_celeprityindex_weibo as b on w.id = b.weibo_id')
				->field($account_weibo_fields.','.$celeprityindex_weibo_fields)
				->select();
			}else{
				$list = $this->where($where)
				->table('app_account_weibo as w')
				->join('app_celeprityindex_weibo as b on w.id = b.weibo_id')
				->limit($now_page,$limit)
				->field($account_weibo_fields.','.$celeprityindex_weibo_fields)
				->select();
			}
			
			
			//微博名人导航分类
			$tags_ids = C('Big_Nav_Class_Ids.celebrity_tags_ids');
			$CategoryTagsInfo = D('CategoryTags')->get_classify_data($tags_ids['top_parent_id']);
			$data['phd'] = $CategoryTagsInfo[$tags_ids['phd']];	//配合度
			$data['mrzy'] = $CategoryTagsInfo[$tags_ids['mrzy']];	//名人职业
			$data['mtly'] = $CategoryTagsInfo[$tags_ids['mtly']];	//名人领域
			
			//排序按照val排序数据
			foreach ($data as $key=>$info) {
				$data[$key] = regroupKey($info,'val',true);
			}
			
			if($list == true) {
				foreach ($list as $key=>$val) {
					
					//是否收藏
					$list[$key]['pg_sc'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>1,'weibo_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity,'pt_type'=>$type));
					//是否拉黑
					$list[$key]['pg_lh'] = $Blackorcollection->check_is_sc_or_lh(array('user_id'=>$id,'or_type'=>0,'weibo_id'=>$val['bs_id'],'is_celebrity'=>$is_celebrity,'pt_type'=>$type));
					
					
					//重新计算价格
					$list[$key]['bs_ck_money'] = $val['bs_ck_money'] + ($gloubid * $val['bs_ck_money']);

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
					//$mrxb = $data['mrxb'][$val['sy_sex']]['title'];
					//$list[$key]['pg_mrxb_explain'] = $mrxb ? $mrxb : '不限';
						
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
			//价格
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
			//地方名人/媒体
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
			//战略合作媒体
			if($addslArray['zlhzmr_mt']!='')
			{
				$wheres['b.strategic_c'] = $addslArray['zlhzmr_mt'];
			}
			//搜索框的账号名
			$account_name = trim($addslArray['account']);
			if($account_name!='')
			{
				$wheres['w.account_name'] = array('like','%'.$account_name.'%');
			}
			return $wheres;
		}

		//获得直发转发 硬广软广价格
		public function getRYMoney($zz,$yr,$account_id)
		{
			if($account_id!='')
			{
				//硬广为1 软广为2
				switch ($yr) {
					case 1:
						//发送类型 1是直发 2是转发
						if($zz==1)
						{
							$value = $this->where(array('id'=>$account_id))->field('yg_zhifa')->find();
							return $value['yg_zhifa'];
						}else{
							$value = $this->where(array('id'=>$account_id))->field('yg_zhuanfa')->find();
							return $value['yg_zhuanfa'];
						}
					break;
					case 2:
						//发送类型 1是直发 2是转发
						if($zz==1)
						{
							$value = $this->where(array('id'=>$account_id))->field('rg_zhifa')->find();
							return $value['rg_zhifa'];
						}else{
							$value = $this->where(array('id'=>$account_id))->field('rg_zhuanfa')->find();
							return $value['rg_zhuanfa'];
						}
					break;
				}
			}
		}

		//获取参考价格
		public function getCkMoney($account_id)
		{
			if($account_id!='')
			{
				$money  = $this->where(array('id'=>$account_id))->field('ck_money')->find();
				return $money['ck_money'];
			}
		}


		//获得微博号所属的媒体主
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
			$app_account_weibo = parent::field_add_prefix('AccountWeibo','bs_','w.');
			if($is_type==0)
			{
				$app_grassroots_weibo = parent::field_add_prefix('GrassrootsWeibo','sy_','g.');
				$field = $app_account_weibo . ',' . $app_grassroots_weibo;
				return $this->table('app_account_weibo as w')
				->where(array('w.id'=>$account_id))
				->join('app_grassroots_weibo as g on g.weibo_id = w.id')
				->field($field)->find();
			}else{
				$app_celeprityindex_weibo = parent::field_add_prefix('CeleprityindexWeibo','sy_','c.');
				
				$field = $app_account_weibo . ',' . $app_celeprityindex_weibo;
				return $this->table('app_account_weibo as w')
				->where(array('w.id'=>$account_id))
				->join('app_celeprityindex_weibo as c on c.weibo_id = w.id')
				->field($field)->find();
			}
		}
	}