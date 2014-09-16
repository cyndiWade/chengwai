<?php
	
	class FastindexWeiboModel extends AdvertBaseModel()
	{


		//接受参数
		public function getPostArray($array)
		{
			//过滤数据
			$addslArray = array();
			foreach($array as $key=>$value)
			{
				$addluArray[$key] = addslashes($value);
			}
			$where = array();
			//处理分类数据 常见分类
			if($addslArray['cjfl']!='')
			{
				$where['common'] = $addslArray['cjfl'];
			}
			//价格区间
			if($addslArray['jgqj']!='')
			{
				$price = explode('-', $addslArray['jgqj']);
				if(empty($price['1']))
				{
					$where['price_height'] = ''
				}
			}
			
			//判断是分页提交还是分栏提交
			if($addslArray['p']!='' &&　$addslArray['count']!='')
			{
				//总数
				$count = $addslArray['count'];
				//分页 p
				$p = $addslArray['p'];
				$p_limit = ($p - 1) * 10;
				$limit = 10;
			}else{

			}
		}


	}