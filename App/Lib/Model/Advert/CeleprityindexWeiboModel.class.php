<?php
	//名人表

	class CeleprityindexWeiboModel extends AdvertBaseModel
	{

		//接受参数 返回草根信息数据 | $type区分新浪 1 或者腾讯 2  | 用户ID
		public function getPostArray($array,$type,$id)
		{
			
			if(!empty($array))
			{
				//过滤数据
				$addvalue = array();
				foreach($array as $key=>$value)
				{
					$addvalue[$key] = addslashes($value);
				}
				//组合生成查询SQL
				$where = $this->getWhere($addvalue);
				//草根表 微博人类型 1 是新浪 2是腾讯
				$where['b.pt_type'] = $type;
				//为名人 传参 1
				$where['b.is_celebrity'] = 1;
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
					$returnList = $this->setSql($addvalue,$p_limit,$limit,$where,$type,$id,1);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setSql($addvalue,0,$limit,$where,$type,$id,1);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询	
		private function setSql($addvalue,$now_page,$limit,$where,$type,$id,$is_celebrity)
		{
			//查询出该用户拉黑的名单
			$Blackorcollection = D('BlackorcollectionWeibo');
			if($addvalue['ckhmd']==1)
			{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.weibo_id'] = array('IN',$weiboId_array);
				}else{
					return false;
				}
			}else if($addvalue['cksc']==1)
			{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,1);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.weibo_id'] = array('IN',$weiboId_array);
				}else{
					return false;
				}
			}else{
				$weiboId_array = $Blackorcollection->getAdvertUser($type,$id,$is_celebrity,0);
				//去除黑名单的weibo_id
				if(!empty($weiboId_array))
				{
					$where['w.weibo_id'] = array('NOT IN',$weiboId_array);
				}
			}
			$count = $this->where($where)
			->table('app_celeprityindex_weibo as w')
			->join('app_account_weibo as b on b.id = w.weibo_id')
			->count();
			//差集统计长度
			$list = $this->where($where)
			->table('app_celeprityindex_weibo as w')
			->join('app_account_weibo as b on b.id = w.weibo_id')
			->limit($now_page,$limit)->field('b.*')->select();
			return array('list'=>$list,'count'=>$count);
		}
		
		//生成不同的WHERE子句
		private function getWhere($addslArray)
		{
			$wheres = array();
			//名人职业
			if($addslArray['mrzy']!='')
			{
				$wheres['w.occupation'] = $addslArray['mrzy'];
			}
			//媒体领域
			if($addslArray['mtly']!='')
			{
				$wheres['w.field'] = $addslArray['mtly'];
			}
			//价格
			if($addslArray['ckbj_type']!='' && $addslArray['jg']!='')
			{
				switch ($addslArray['ckbj_type']) {
					case 1:
						$wheres['w.ck_price'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
				}
			}
			//地方名人/媒体
			if($addslArray['dfmr_mt']!='')
			{
				$wheres['w.cirymedia'] = $addslArray['dfmr_mt'];
			}
			//兴趣标签
			if($addslArray['xqbq']!='')
			{
				$wheres['w.interest'] = $addslArray['xqbq'];
			}
			//暂略合作名人
			if($addslArray['zlhzmr_mt']!='')
			{
				$wheres['w.strategic_c'] = $addslArray['zlhzmr_mt'];
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