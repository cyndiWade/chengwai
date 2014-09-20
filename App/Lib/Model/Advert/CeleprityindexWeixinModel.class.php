<?php
	//微信草根表

	class CeleprityindexWeixinModel extends AdvertBaseModel
	{

		//接受参数 返回草根信息数据 | $type区分新浪 1  腾讯 2  微信 3 新闻 4 | 用户ID
		public function getPostArray($array,$id)
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
				$where = $this->getWhere($addvalue);
				//为草根 传参 1
				$where['b.is_celebrity'] = 1;
				//判断是分页提交还是分栏提交
				$limit = 10;
				//如果有分页参数
				if( $addvalue['p']!='' )
				{
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$returnList = $this->setSql($addvalue,$p_limit,$limit,$where,$id,1);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setSql($addvalue,0,$limit,$where,$id,1);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setSql($addvalue,$now_page,$limit,$where,$id,$is_celebrity)
		{
			//查询出该用户拉黑的名单
			$Blackorcollection = D('BlackorcollectionWeixin');
			if($addvalue['ckhmd']==1)
			{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.weixin_id'] = array('IN',$weixinId_array);
				}else{
					return false;
				}
			}else if($addvalue['cksc']==1)
			{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,1);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.weixin_id'] = array('IN',$weixinId_array);
				}else{
					return false;
				}
			}else{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.weixin_id'] = array('NOT IN',$weixinId_array);
				}
			}
			$count = $this->where($where)
			->table('app_celeprityindex_weixin as w')
			->join('app_account_weixin as b on b.id = w.weixin_id')
			->count();
			//差集统计长度
			$list = $this->where($where)
			->table('app_celeprityindex_weixin as w')
			->join('app_account_weixin as b on b.id = w.weixin_id')
			->limit($now_page,$limit)->field('*')->select();
			return array('list'=>$list,'count'=>$count);
		}
		
		//生成不同的WHERE子句
		private function getWhere($addslArray)
		{
			$wheres = array();
			//处理分类数据 常见分类
			if($addslArray['cjfl']!='')
			{
				$wheres['w.common'] = $addslArray['cjfl'];
			}
			//粉丝数量
			if($addslArray['fans_num']!='')
			{
				$wheres['w.fans_num'] = $this->getLeftRightstr($addslArray['fans_num'],'-');
			}
			//价格区间
			if($addslArray['zfjg_type']!='' && $addslArray['jg']!='')
			{
				switch ($addslArray['zfjg_type']) {
					case 1:
						$wheres['w.yg_zhuanfa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 2:
						$wheres['w.yg_zhifa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 3:
						$wheres['w.rg_zhuanfa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 4:
						$wheres['w.rg_zhifa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
				}
			}
			//性别区分
			if($addslArray['fans_sex']!='')
			{
				$wheres['w.fans_sex'] = $addslArray['fans_sex'];
			}
			//为您推荐
			if($addslArray['tj']!='')
			{
				$wheres['w.recommend'] = 1;
			}
			//热门微博
			if($addslArray['rmwb']!='')
			{
				$wheres['w.is_hot'] = 1;
			}
			//折扣
			if($addslArray['xstj']!='')
			{
				$wheres['w.specialoffer'] = 1;
			}
			//搜索框的账号名
			$account_name = trim($addslArray['account']);
			if($account_name!='')
			{
				$wheres['b.account_name'] = array('like','%'.$account_name.'%');
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

	}