<?php


	class AccountWeiboModel extends AdvertBaseModel
	{

		//接受参数 返回草根信息数据 | $type区分新浪 1 或者腾讯 2  | 用户ID
		public function getPostcgArray($array,$type,$id)
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
					$returnList = $this->setcgSql($addvalue,$p_limit,$limit,$where,$type,$id,0);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setcgSql($addvalue,0,$limit,$where,$type,$id,0);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setcgSql($addvalue,$now_page,$limit,$where,$type,$id,$is_celebrity)
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
			$count = $this->where($where)
			->table('app_account_weibo as w')
			->join('app_grassroots_weibo as b on w.id = b.weibo_id')
			->count();
			//差集统计长度
			$list = $this->where($where)
			->table('app_account_weibo as w')
			->join('app_grassroots_weibo as b on w.id = b.weibo_id')
			->limit($now_page,$limit)->field('w.*,b.common,b.sex')->select();
			return array('list'=>$list,'count'=>$count);
		}
		
		//生成不同的WHERE子句
		private function getcgWhere($addslArray)
		{
			$wheres = array();
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
			//性别区分
			if($addslArray['fans_sex']!='')
			{
				$wheres['b.fans_sex'] = $addslArray['fans_sex'];
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
		public function getPostmrArray($array,$type,$id)
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
					$returnList = $this->setmrSql($addvalue,$p_limit,$limit,$where,$type,$id,1);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setmrSql($addvalue,0,$limit,$where,$type,$id,1);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setmrSql($addvalue,$now_page,$limit,$where,$type,$id,$is_celebrity)
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
			$count = $this->where($where)
			->table('app_account_weibo as w')
			->join('app_celeprityindex_weibo as b on w.id = b.weibo_id')
			->count();
			//差集统计长度
			$list = $this->where($where)
			->table('app_account_weibo as w')
			->join('app_celeprityindex_weibo as b on w.id = b.weibo_id')
			->limit($now_page,$limit)->field('w.*,b.occupation,b.ck_price,b.yc_price,b.field,b.coordination,b.fansnumber,b.strategic_c,b.originality')->select();
			return array('list'=>$list,'count'=>$count);
		}
		
		//生成不同的WHERE子句
		private function getmrWhere($addslArray)
		{
			$wheres = array();
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
				$wheres['b.cirymedia'] = $addslArray['dfmr_mt'];
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
	}