<?php

/**
 * 活动订单类
 * 
 */
class GeneralizeOrderModel extends AppBaseModel
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
    	$info = $this->field("id, `users_id`, `tfpt_type`, `fslx_type`, `ryg_type`, `hd_name`, `zf_url`, `zfy_type`, `zfnr_type`, `start_time`, `over_time`, `create_time`, bz_info, zw_info")
    	->where(array("id"=>$order_id))->find();
    	
    	return $info;
    }
	
	/**
     * 获取订单列表
     * 
     * @param int   $userid 用户ID
     * @param int   $status 订单帐号状态
     * @param array $where  条件

     * @author lurongchang
     * @date   2014-10-12
     * @return array
     */
    public function getOrderList($userid, $status, $where)
    {
        // 获取订单
        $order = $this->field(true)->where($where)->select();
        
        $orderids = array();
        $datas = array();
        if ($order) {
            $newOrderList = array();
            foreach ($order AS $val) {
                $orderids[] = $val['id'];
                $newOrderList[$val['id']] = $val;
            }
            unset($order);
            $where = array(
                'audit_status' => $status,
                'generalize_id' => array('IN', $orderids),
            );
            // 获取订单子表对应的帐号ID [一个订单对应多个帐号]
            $orderAccountList = M('GeneralizeAccount')->where($where)
                ->field('id, generalize_id, price, account_id, account_type, audit_status')->select();
         
            $accountIds = array();
            if ($orderAccountList) {
                foreach ($orderAccountList AS $info) {
                    $accountIds[] = $info['account_id'];
                }
                $where = array(
                    'users_id' => $userid,
                    'id' => array('IN', $accountIds)
                );
                // 获取订单微博帐号
                $accountList = D('AccountWeibo')->getAccountList($where, 'id, account_name');
                $accountInfo = array();
                if ($accountList) {
                    foreach ($accountList AS $val) {
                        $accountInfo[$val['id']] = $val['account_name'];
                    }
                }
                
                foreach ($orderAccountList AS $info) {
                    if (isset($accountInfo[$info['account_id']])) {
                        $accountName = $accountInfo[$info['account_id']];
                    } else {
                        continue;
                    }
                    $orderInfo = $newOrderList[$info['generalize_id']];
                    $temp = array(
                        'child_order_id'=> $info['id'],
                        'order_id'      => $info['generalize_id'],
                        'account_name'  => $accountName,
                        'account_type'  => getFsT($orderInfo['fslx_type']),
                        'type_info'     => 'weibo',
                        'title'         => $orderInfo['hd_name'],
                        'price'         => $info['price'],
                        'start_time'    => date('Y-m-d', $orderInfo['start_time']),
                        'order_status'  => $info['audit_status'],
                        'order_status_name'  => getAccountStatus($info['audit_status']),
                        'mark'          => $orderInfo['bz_info'],
                        'is_time_now'	=>  $orderInfo['start_time'] < time() ? true : false
                    );
                    $datas[] = $temp;
                }
            }
        }
        
        return $datas;
    }
}