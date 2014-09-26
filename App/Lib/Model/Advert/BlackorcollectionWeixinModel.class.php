<?php
	//拉黑 或者 收藏

	class BlackorcollectionWeixinModel extends AdvertBaseModel
	{

		//获得用户拉黑微信ID  用户名id 是否是名人 是收藏还是黑名单
		public function getAdvertUser($id,$is_celebrity,$or)
		{
			$where = array('user_id'=>$id,'is_celebrity'=>$is_celebrity,'or_type'=>$or);
			$volist = $this->where($where)->field('weixin_id')->select();
			$collection = array();
			foreach($volist as $value)
			{
				$collection[] = $value['weixin_id'];
			}
			return $collection;
		}

		//拉黑 or 收藏
		public function insertBlackorcollection($array,$user_id)
		{
			$addDate = array(
				'is_celebrity'=>$array['is_celebrity'],
				'or_type'=>$array['or_type'],
				'user_id'=>$user_id,
				'weixin_id'=>$array['weixin_id']
			);
			$count = $this->where($addDate)->count();
			if($count==0)
			{
				$this->add($addDate);
				return true;
			}else{
				return false;
			}
			
		}

	}