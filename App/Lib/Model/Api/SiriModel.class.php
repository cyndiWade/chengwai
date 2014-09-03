<?php
class SiriModel extends AppBaseModel{
	
    //获取一条数据
	public function seek_one_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	
    public function seek_explain ($condition,$field = '*') {
		$con = array('is_del'=>0,'type'=>2);
		array_add_to($con,$condition);

		return $this->where($con)->getField('explain');
	}
	
    public function seek_city () {
		$con = array('is_del'=>0,'type'=>1);
		$data =$this->where($con)->field($field)->select();
		foreach($data as $key=>$val){
			$arr[] = $val['keyword'];
		}
		return $arr;
	}
	
}