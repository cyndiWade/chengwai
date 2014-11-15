<?php
	//

	class NewsViewModel extends AdvertBaseModel
	{
	
		public function get_one ($where) {
			return $this->where($where)->find();
		}
		
		
		public function add_data ($users_id) {
			$this->create();
			$this->users_id = $users_id;
			$this->create_time = time();
			$this->start_time = strtotime($this->start_time);
			return $this->add();
		}
		
	}