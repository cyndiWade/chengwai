<?php
class PropertyAction extends HotelBaseAction{
	
	public function index(){
	    $url = $_SERVER["REQUEST_URI"];
	    $this->assign('hover',parent::getAction($url));
		$this->display();
	}
}