<?php
	//拉黑 或者 收藏

	class BlackorcollectionNewsModel extends AdvertBaseModel
	{

		//获得用户拉黑新闻ID 用户名id 是收藏还是黑名单
		public function getAdvertUser($id,$or)
		{
			$where = array('user_id'=>$id,'or_type'=>$or);
			$volist = $this->where($where)->field('news_id')->select();
			$collection = array();
			foreach($volist as $value)
			{
				$collection[] = $value['news_id'];
			}
			return $collection;
		}

		//拉黑 or 收藏
		public function insertBlackorcollection($array,$user_id)
		{
			$addDate = array(
				'or_type'=>$array['or_type'],
				'user_id'=>$user_id,
				'news_id'=>$array['news_id']
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