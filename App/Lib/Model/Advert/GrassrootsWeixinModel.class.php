<?php
	//微信草根表

	class GrassrootsWeixinModel extends AdvertBaseModel
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
				//为草根 传参 0
				$where['b.is_celebrity'] = 0;
				//判断是分页提交还是分栏提交
				$limit = 10;
				//如果有分页参数
				if( $addvalue['p']!='' )
				{
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$returnList = $this->setSql($addvalue,$p_limit,$limit,$where,$id,0);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setSql($addvalue,0,$limit,$where,$id,0);
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
			->table('app_grassroots_weixin as w')
			->join('app_account_weixin as b on b.id = w.weixin_id')
			->count();
			//差集统计长度
			$list = $this->where($where)
			->table('app_grassroots_weixin as w')
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
			//价格区间
			if($addslArray['zfjg_type']!='' && $addslArray['jg']!='')
			{
				switch ($addslArray['zfjg_type']) {
					case 1:
						$wheres['w.more_ying_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 2:
						$wheres['w.more_ruang_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 3:
						$wheres['w.one_yingg_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 4:
						$wheres['w.one_ruangg_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 5:
						$wheres['w.more_twoy_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 6:
						$wheres['w.more_twor_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 7:
						$wheres['w.more_ny_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 8:
						$wheres['w.more_nr_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
				}
			}
			//粉丝数量
			if($addslArray['fans_num']!='')
			{
				$wheres['w.fans_number'] = $this->getLeftRightstr($addslArray['fans_num'],'-');
			}
			//视频认证  粉丝量已认证
			if($addslArray['fs_num_rz']!='')
			{
				$wheres['w.video_fans_on'] = $addslArray['fs_num_rz'];
			}
			//视频认证   受众性别已认证	
			if($addslArray['fs_sex_rz']!='')
			{
				$wheres['w.video_sex_on'] = $addslArray['fs_sex_rz'];
			}
			//账号是否认证
			if($addslArray['zhsfrz']!='')
			{
				$wheres['w.account_on'] = $addslArray['zhsfrz'];
			}
			//受众性别
			if($addslArray['szxb']!='')
			{
				$sex = substr($addslArray['szxb'],0,1);
				$bfb = substr($addslArray['szxb'],1,2);
				if($sex=='x')
				{
					$wheres['w.audience_man'] = array('GT',$bfb);
				}else{
					$wheres['w.audience_women'] = array('GT',$bfb);
				}
			}
			//粉丝量认证时间
			if($addslArray['fsrzsj']!='')
			{
				$wheres['w.fans_c_time'] = array('LT',$addslArray['fsrzsj']);
			}
			//周平均阅读数
			if($addslArray['zpjyds']!='')
			{
				$wheres['w.fans_c_time'] = $this->getLeftRightstr($addslArray['zpjyds'],'-');
			}
			//为您推荐
			if($addslArray['tj']!='')
			{
				$wheres['w.recommend'] = 1;
			}
			//热门微博
			if($addslArray['rmwx']!='')
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