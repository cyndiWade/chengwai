<?php

/**
 * 活动订单下子媒体类
 * 
 */
class GeneralizeWeixinAccountModel extends MediaBaseModel
{
	/**
	 *  更新媒体订单状态
	 *
	 * @param int   $id		媒体账号ID
	 * @param int   $status	状态值
	 * 
	 * @author bumtime 2014-10-01
	 * @return bool
	 */
	public function setAccountStatus($id, $status)
	{
		$where['id'] = $id;
		$data['audit_status'] = $status;
		$return = $this->where($where)->save($data);
		return $return > 0  ? true : false;
	}
	
	/**
	 *  取媒体账号详情
	 *
	 * @param int   $id		媒体账号ID
	 * @param int   $status	状态值
	 * 
	 * @author bumtime 2014-10-04
	 * @return array
	 */
	public function getInfoById($id, $fields="*")
	{
		$where['id'] = $id;
		$return = $this->where($where)->field($fields)->find();
		return $return ;
	}
	
}