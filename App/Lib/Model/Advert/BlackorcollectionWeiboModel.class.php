<?php
	//拉黑 或者 收藏

	class BlackorcollectionWeiboModel extends AdvertBaseModel
	{

		//获得用户拉黑微博ID 平台类型 用户名id 是否是名人 是收藏还是黑名单
		public function getAdvertUser($type,$id,$is_celebrity,$or)
		{
			$where = array('pt_type'=>$type,'user_id'=>$id,'is_celeprity'=>$is_celebrity,'or_type'=>$or);
			$volist = $this->where($where)->field('weibo_id')->select();
			$collection = array();
			foreach($volist as $value)
			{
				$collection[] = $value['weibo_id'];
			}
			return $collection;
		}


	}