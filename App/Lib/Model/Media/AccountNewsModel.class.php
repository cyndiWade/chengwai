<?php
// 新闻媒体账号模型
class AccountNewsModel extends MediaBaseModel 
{
	/**
     * 批量导入社交帐号
     * 
     * @author lurongchang
     * @date   2014-09-20
     * @return void
     */
    public function addBatchAccount($data)
    {
        if (empty($data)) {
            return false;
        }
        foreach ($data AS $val) {
            if (count($val) != 18) {
                continue;
            }
            $insertData[] = array(
                'users_id'              => intval($val[0]),
                'big_type'              => intval($val[1]),
                'pt_type'               => intval($val[2]),
                'web_type'              => intval($val[3]),
                'week_order_num'        => intval($val[4]),
                'month_order_nub'       => intval($val[5]),
                'audit_status'          => intval($val[6]),
                'receiving_status'      => intval($val[7]),
                'putaway_status'        => intval($val[8]),
                'tmp_receiving_status'  => intval($val[9]),
                'is_yg_status'          => intval($val[10]),
                'receiving_num_status'  => intval($val[11]),
                'receiving_num'         => intval($val[12]),
                'url_type'              => intval($val[13]),
                'url_status'            => intval($val[14]),
                'integral'              => intval($val[15]),
                'status'                => intval($val[16]),
                'is_del'                => intval($val[17]),
                'create_time'           => $_SERVER['REQUEST_TIME'],
            );
        }
        return $insertData ? $this->addAll($insertData) : false;
    }
    
    /**
     * 获取某帐号信息
     * 
     * @param array  $where 条件
     * @param string $field 字段 默认ture为获取所有字段
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
    public function getAccountInfo($where, $field = true)
    {
        return $this->where($where)->field($field)->find();
    }
    
    /**
     * 增加帐号
     * 
     * @param array  $data 数据
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
    public function addAccount($datas)
    {
        if (empty($datas['users_id']) || empty($datas['account_name'])) {
            return false;
        }
        $insertId = $this->add($datas);
        return $insertId;
    }
    
    /**
     * 获取帐号列表
     * 
     * @author lurongchang
     * @date   2014-09-22
     * @return void
     */
    public function getLists($where, $order = 'id DESC', $page = 1, $pageSize = 20)
    {
        $list = $this->field(true)->where($where)->order($order)->page($page, $pageSize)->select();
        $count = $this->where($where)->count();
        return array('list' => $list, 'count' => $count);
    }


    /**
     * 根据会员Id获取帐号列表
     * 
     * @param  int 		$user_id 会员ID
     * @param  array 	$where 其他条件
     * 
     * @author bumtime
     * @date   2014-10-07

     * @return array	
     */
    public function getListsByUserID($user_id,  $where = array())
    {
    	$where["users_id"] = $user_id ;
    	$list = $this->where($where)->getField("id, account_name");
    	return $list;
    }
    
    /**
     * 检查账号是否属于该会员
     * 
     * @param  int 	$account_id 媒体账号ID
     * @param  int 	$user_id 	会员ID
     * 
     * @author bumtime
     * @date   2014-10-07

     * @return bool	
     */
    public function checkAccountByUserId($account_id, $user_id)
    {
    	$users_id_new = $this->where(array("id"=>$account_id))->getField("users_id");
    	return $users_id_new == $user_id ? true : false;
    }
    
    /**
     * 批量获取某帐号数据
     * 
     * @param array  $where 条件
     * @param string $field 字段 默认true为提取所有字段
     * 
     * @author lurongchang
     * @date   2014-10-11
     * @return void
     */
    public function getAccountList($where, $field = true)
    {
        $datas = $this->where($where)->field($field)->select();
        return $datas;
    }
}