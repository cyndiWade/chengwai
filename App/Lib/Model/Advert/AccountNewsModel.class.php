<?php
	//新闻信息表

	class AccountNewsModel extends AdvertBaseModel
	{

		//接受参数 返回新闻信息数据
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
				//判断是分页提交还是分栏提交
				$limit = 10;
				//如果有分页参数
				if( $addvalue['p']!='' )
				{
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$returnList = $this->setSql($addvalue,$p_limit,$limit,$where,$id);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setSql($addvalue,0,$limit,$where,$id);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setSql($addvalue,$now_page,$limit,$where,$id)
		{
			//查询出该用户拉黑的名单
			$Blackorcollection = D('BlackorcollectionNews');
			if($addvalue['ckhmd']==1)
			{
				$newsId_array = $Blackorcollection->getAdvertUser($id,0);
				//去除黑名单的weibo_id
				if(!empty($newsId_array))
				{
					$where['w.id'] = array('IN',$newsId_array);
				}else{
					return false;
				}
			}else if($addvalue['cksc']==1)
			{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,1);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('IN',$newsId_array);
				}else{
					return false;
				}
			}else{
				$weixinId_array = $Blackorcollection->getAdvertUser($id,0);
				//去除黑名单的weibo_id
				if(!empty($weixinId_array))
				{
					$where['w.id'] = array('NOT IN',$newsId_array);
				}
			}
			$where['w.is_del'] = 0;
			$count = $this->where($where)
			->table('app_account_news as w')
			->join('app_index_news as b on w.id = b.news_id')
			->count();
			//差集统计长度
			$list = $this->where($where)
			->table('app_account_news as w')
			->join('app_index_news as b on w.id = b.news_id')
			->limit($now_page,$limit)->field('w.*')->select();
			return array('list'=>$list,'count'=>$count);
		}
		
		//生成不同的WHERE子句
		private function getWhere($addslArray)
		{
			$wheres = array();
			//行业分类
			if($addslArray['hyfl']!='')
			{
				$wheres['b.classification'] = $addslArray['hyfl'];
			}
			//地区筛选
			if($addslArray['dqsx']!='')
			{
				$wheres['b.area'] = $addslArray['dqsx'];
			}
			//优惠专区
			if($addslArray['yhzq']!='')
			{
				$wheres['b.discount'] = $addslArray['yhzq'];
			}
			//价格
			if($addslArray['jg']!='')
			{
				$wheres['b.price'] = $this->getLeftRightstr($addslArray['jg'],'-');
			}
			//是否新闻源
			if($addslArray['sfxwy']!='')
			{
				$wheres['b.is_news'] = $addslArray['sfxwy'];
			}
			//门户类型
			if($addslArray['mh_type']!='')
			{
				$wheres['b.type_of_portal'] = $addslArray['mh_type'];
			}
			//带链接情况
			if($addslArray['dljzk']!='')
			{
				$wheres['b.links'] = $addslArray['dljzk'];
			}
			//推荐情况
			if($addslArray['tj']!='')
			{
				$wheres['b.recommend'] = $addslArray['tj'];
			}
			//热门微博
			if($addslArray['rmwx']!='')
			{
				$wheres['b.is_hot'] = $addslArray['rmwx'];
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

	}