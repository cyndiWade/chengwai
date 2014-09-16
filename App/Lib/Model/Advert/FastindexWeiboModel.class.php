<?php
	
	class FastindexWeiboModel extends AdvertBaseModel
	{

		//接受参数
		public function getPostArray($array)
		{
			//过滤数据
			$addvalue = array();
			foreach($array as $key=>$value)
			{
				$addvalue[$key] = addslashes($value);
			}
			if(!empty($addvalue))
			{
				$where = $this->getWhere($addvalue);
				//判断是分页提交还是分栏提交
				$limit = 10;
				if( ($addvalue['p']!='')  &&　($addvalue['count']!='') )
				{
					//总数
					$count = $addvalue['count'];
					//分页 p
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$list = $this->where($where)->table('app_fastindex_weibo as w')
					->join('app_account_weibo as b on b.id = w.weibo_id')
					->limit($p_limit,$limit)->field('b.*')->select();
					return $new_list = array('list'=>$list,'p'=>$p,'count'=>$count);
				}else{
					$count = $this->where($where)->count();
					$list = $this->where($where)->table('app_fastindex_weibo as w')
					->join('app_account_weibo as b on b.id = w.weibo_id')
					->limit(0,$limit)->field('b.*')->select();
					return $new_list = array('list'=>$list,'p'=>1,'count'=>$count);
				}
			}
		}

		//生成不同的WHERE子句
		private function getWhere($addslArray)
		{
			$wheres = array();
			//处理分类数据 常见分类
			if($addslArray['cjfl']!='')
			{
				$wheres['common'] = $addslArray['cjfl'];
			}
			//粉丝数量
			if($addslArray['fans']!='')
			{
				$wheres['fans_num'] = $this->getLeftRightstr($addslArray['fans'],'-');
			}
			//价格区间
			if($addslArray['zfjg_type']!='')
			{
				switch ($addslArray['zfjg_type']) {
					case 1:
						$wheres['yg_zhuanfa'] = $this->getLeftRightstr($addslArray['fans'],'-');
					break;
					case 2:
						$wheres['yg_zhuanfa'] = $this->getLeftRightstr($addslArray['fans'],'-');
					break;
					case 3:
						$wheres['yg_zhuanfa'] = $this->getLeftRightstr($addslArray['fans'],'-');
					break;
					case 4:
						$wheres['yg_zhuanfa'] = $this->getLeftRightstr($addslArray['fans'],'-');
					break;
				}
			}
			//性别区分
			if($addslArray['sex']!='')
			{
				$wheres['sex'] = 1;
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