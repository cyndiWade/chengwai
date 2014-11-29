<?php
	//拉黑 或者 收藏

	class BlackorcollectionWeiboModel extends AdvertBaseModel
	{

		//获得用户拉黑微博ID 平台类型 用户名id 是否是名人 是收藏还是黑名单
		public function getAdvertUser($type,$id,$is_celebrity,$or)
		{
			$where = array('pt_type'=>$type,'user_id'=>$id,'is_celebrity'=>$is_celebrity,'or_type'=>$or);
			$volist = $this->where($where)->field('weibo_id')->select();
			$collection = array();
			foreach($volist as $value)
			{
				$collection[] = $value['weibo_id'];
			}
			return $collection;
		}

		//拉黑 or 收藏
		public function insertBlackorcollection($array,$user_id)
		{
			$addDate = array(
				'pt_type'=>$array['pt_type'],
				'is_celebrity'=>$array['is_celebrity'],
				'or_type'=>$array['or_type'],
				'user_id'=>$user_id,
				'weibo_id'=>$array['weibo_id']
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
		
		//删除逻辑关系
		public function deleteBlackorcollection($array,$user_id) {
			$where = array(
					'pt_type'=>$array['pt_type'],
					'is_celebrity'=>$array['is_celebrity'],
					'or_type'=>$array['or_type'],
					'user_id'=>$user_id,
					'weibo_id'=>$array['weibo_id']
			);
			return $this->where($where)->delete();
		}
		
		
		public function check_is_sc_or_lh ($array) {
			$where = array(
					'pt_type'=>$array['pt_type'],
					'is_celebrity'=>$array['is_celebrity'],
					'or_type'=>$array['or_type'],
					'user_id'=>$array['user_id'],
					'weibo_id'=>$array['weibo_id']
			);
			$count = $this->where($where)->count();
			if($count > 0) {
				return 1;
			} else {
				return 0;
			}
		}

	}