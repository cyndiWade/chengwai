<?php
// 微信公众号模型
class AccountWeixinModel extends MediaBaseModel 
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
                'account_name'          => trim($val[3]),
                'fans_num'              => intval($val[4]),
                'dtb_money'             => floatval($val[5]),
                'dtwdyt_money'          => floatval($val[6]),
                'dtwdet_money'          => floatval($val[7]),
                'dtwqtwz_money'         => floatval($val[8]),
                'dj_money'              => floatval($val[9]),
                'week_order_num'        => intval($val[10]),
                'month_order_nub'       => intval($val[11]),
                'audit_status'          => intval($val[12]),
                'receiving_status'      => intval($val[13]),
                'putaway_status'        => intval($val[14]),
                'tmp_receiving_status'  => intval($val[15]),
                'status'                => intval($val[16]),
                'is_del'                => intval($val[17]),
                'create_time'           => $_SERVER['REQUEST_TIME'],
            );
        }
        return $insertData ? $this->addAll($insertData) : false;
    }
    
    /**
     * 获取帐号列表
     * 
     * @author lurongchang
     * @date   2014-09-22
     * @return void
     */
    public function getLists($where, $page = 1, $pageSize = 20)
    {
        $list = $this->field(true)->where($where)->page($page, $pageSize)->select();
        $count = $this->where($where)->count();
        return array('list' => $list, 'count' => $count);
    }
}