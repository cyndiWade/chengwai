<?php
class KfLogModel extends AdminBaseModel {

	  public function add_log($data){
	  	 
	  	 return $this->add($data);
	  	 //return $this->getLastInsertID();
	  }
	  
	  public function get_exist($kf_id,$order_id){
	  	  
	  	 return $this->where(array('order_id'=>$order_id,'kf_id'=>$kf_id))->select();
	  }
	  
	  public function get_done($order_id){
	  	$data = $this->where(array('order_id'=>$order_id))->field('kf_id')->select();
	    $arr = array();
	  	foreach($data as $k=>$v){
	    	$arr[] = $v['kf_id'];
	    }

	    return array_unique($arr);
	  }
	 public function get_all_data($order_id,$field='*'){
	  	$data = $this->field($field)
			->table($this->prefix.'kf_log AS ol')
			->join($this->prefix.'users AS u ON u.id=ol.user_id')
			->where(array('ol.order_id'=>$order_id))
			->select();
			parent::set_all_time($data, array('addtime'));
			$arr = C('KF');
			foreach($data as $k=>$v){
				$data[$k]['kf_id'] = $arr[$v['kf_id']]['explain'];
			}
			return $data;
	  }
}