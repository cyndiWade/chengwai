<?php

//二维码模型表
class WxCodeModel extends AdminBaseModel {
	

	public function seek_hotel_codes ($condition,$field = '*') {
		$con = 'wc.is_del=0 and '.$condition;
		
		
		//计算数据条数
		$count = $this->field('wx.id')
		->table($this->prefix.'wx_code AS wc')
		->join($this->prefix.'hotel AS h ON h.id = wc.hotel_id')
		->where($con)
		->count();
		
		//分页
		$Page = new Page($count,10);	//分页，每页10条
		
		//查询数据
		$data = $this->field($field)
		->table($this->prefix.'wx_code AS wc')
		->join($this->prefix.'hotel AS h ON h.id = wc.hotel_id')
		->where($con)
		->limit($Page->firstRow.','.$Page->listRows)
		->order('h.id ASC')
		->select();

		return array('obj'=>$Page,'data'=>$data);

	}
	
	//获取一条数据
	public function seek_one_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	//修改一条数据
	public function save_one_code ($code_id) {
		return parent::save_one_data(array('id'=>$code_id))	;
	}

	
}

?>
