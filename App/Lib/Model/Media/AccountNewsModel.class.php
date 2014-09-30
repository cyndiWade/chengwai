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
}