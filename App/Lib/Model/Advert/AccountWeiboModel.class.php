<?php
	//微博数据
	class AccountWeiboModel extends AdvertBaseModel
	{

		//获得总数和直取10条
		public function getListTen()
		{
			$count = $this->count();
			$list = $this->limit('10')->select();
			$returnArray = array('count'=>$count,'list'=>$list);
			return $returnArray;
		}
	}