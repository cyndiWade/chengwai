<?php

/**
 * 活动订单类
 * 
 */
class IntentionWeiboOrderModel extends MediaBaseModel
{
	/**
	 *  取统计信息
	 *
	 * @param int   $userId	会员ID
	 *
	 * @author bumtime 2014-10-01
	 * @return array
	 */
	public function get_OrderInfo_list($id)
	{
		$where['users_id'] = $id;
		return parent::get_spe_data($where);
	}	
	
	 /**
     * 获取订单详情
     * 
     * @param  int 		$order_id 订单ID

     * @author bumtime
     * @date   2014-10-02

     * @return array	微博账号Id
     */
    public function getOrderInfo($order_id)
    {
    	$info = array();
    	$info = $this->field("`id`,`tfpt_type`, `fslx_type`, `ryg_type`, `yxd_name`, `zf_url`, `zfy_type`, `zw_info`, `wa_url_status`, `start_time`,
    	 `over_time`, `sfyq`, `bz_info`, `create_time`, `status`")->where(array("id"=>$order_id))->find();
    	
    	return $info;
    }
}

?>