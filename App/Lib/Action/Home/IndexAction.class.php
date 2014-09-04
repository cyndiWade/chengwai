<?php

class IndexAction extends HomeBaseAction {
	
	//http://senlingongyuan.zhixun.in/14yaoqianshu/
	
	public function index () {
		//echo $this->global_tpl_view['path'];
		$this->display();
	}


	public function get () {
		
		$this->display();
	}
	
	
	public function demo() {
		$this->display();
	}
}

?>