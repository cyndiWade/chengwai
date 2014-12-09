<?php

//媒体主用户信息表
class UserMediaModel extends AdminBaseModel
{

    public function save_info($users_id, $data)
    {
        return $this->where(array('users_id' => $users_id))->save($data);
    }


    /**
     * 媒体主 收入
     * 
     * @param user_id      媒体主ID
     * @param price        金额
     * @param adverttype   订单类型 //新闻1、微信2、微博3
     * @param generalizeid 主订单ID
     * 
     * @author chenchao
     */
    public function insertPirce($user_id, $price, $adverttype, $generalizeid)
    {
        //读取当前金额
        $money = $this->where(array('users_id' => $user_id))->field('money')->find();
        //处理媒体主金额
        $update['money'] = $money['money'] + $price;
        $model = D('UserMedia');
        $model->where(array('users_id' => $user_id))->save($update);
        
        //写收入LOG
        $add['users_id'] = $user_id;
        $add['shop_number'] = 'SL' . time();
        $add['money'] = $price;
        $add['type'] = 5;
        $add['adormed'] = 1;
        $add['member_info'] = '收入';
        $add['admin_info'] = '收入';
        $add['time'] = time();
        $add['adverttype'] = $adverttype;
        $add['generalizeid'] = $generalizeid;
        $add['status'] = 1;
        D('Fund')->add($add);
    }
}
