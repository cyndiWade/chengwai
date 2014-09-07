<?php
/**
 * 首页
 * @author Administrator
 *
 */
class IndexAction extends AdminBaseAction {
    
	
	public function index() {
		$title = '不限,新浪,微信,朋友圈,微视,腾讯微博,微淘';
		$TagsCelebrity  = M('TagsCelebrity');
		
		$title_array = explode(',', $title);
		
		$parent_id = 16;
		$category = 2;
		foreach ($title_array as $key=>$val) {
			$TagsCelebrity->parent_id = $parent_id;
			$TagsCelebrity->category = $category;
			$TagsCelebrity->title = $val;
			//$TagsCelebrity->add();
		}
	}
    
	
	public function aaa() {

	}
	
}