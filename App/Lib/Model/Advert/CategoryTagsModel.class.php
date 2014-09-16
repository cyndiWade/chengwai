<?php

//标签类别表
class CategoryTagsModel extends AdvertBaseModel {
	
	public $data_list = array();
	
	//地柜调用一个父级下所有的子分类数据
	private function seek_parent_list($parent_id) {
		$now_data_list = $this->where(array('parent_id'=>$parent_id,'is_del'=>0,'show_status'=>1))->select();
		
		if ($now_data_list == true) {
			foreach ($now_data_list as $key=>$val) {
				array_push($this->data_list,$val);
				$this->seek_parent_list($val['id']);
			}
		}
	}
	
	/**
	 * 获取子类下所有的分类数据
	 * @param INT $parent_id
	 * @return Array
	 */
	public function get_classify_data ($parent_id) {
		$this->seek_parent_list($parent_id);
		$data = $this->data_list;
		
		$result = array();
		foreach ($data as $key=>$val) {
			$result[$val['parent_id']][] = $val;
		}
		
		return $result;
	}
	
	private function sort_classify () {
		
	} 
	
	
}

?>
