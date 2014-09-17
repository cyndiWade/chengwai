<?php
	//草根表

	class GrassrootsWeiboModel extends AdvertBaseModel
	{

		//接受参数 返回草根信息数据 $type区分新浪 1 或者腾讯 2
		public function getPostArray($array,$type)
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
				//为草根 传参 0
				$where['b.is_celebrity'] = 0;
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
					$returnList = $this->setSql($p_limit,$limit,$where);
					return $new_list = array('list'=>$returnList['list'],'p'=>$p,'count'=>$returnList['count']);
				}else{
					$returnList = $this->setSql(0,$limit,$where);
					return $new_list = array('list'=>$returnList['list'],'p'=>1,'count'=>$returnList['count']);
				}
			}
		}

		//sql查询
		private function setSql($now_page,$limit,$where)
		{
			$count = $this->where($where)
			->table('app_grassroots_weibo as w')
			->join('app_account_weibo as b on b.id = w.weibo_id')
			->count();
			$list = $this->where($where)
			->table('app_grassroots_weibo as w')
			->join('app_account_weibo as b on b.id = w.weibo_id')
			->limit($now_page,$limit)->field('b.*')->select();
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
			if($addslArray['zfjg_type']!='')
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