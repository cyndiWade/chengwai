<?php
class OrderComplainModel extends AdminBaseModel
{
    /**
     * 投诉列表
     * 
     * @author chenchao
     */
    public function getOrderComplainList($where)
    {
        $result = $this->where($where)->order('id DESC')->select();
        parent::set_all_time($result,array('times'));
        return $result;
    }
    
    /**
     * 投诉详情
     * 
     * @author chenchao
     */
    public function getOrderComplainInfo($where)
    {
        $sql = "SELECT c.id, c.media_type, c.content, c.ddid, c.order_id, c.users_id, cc.content AS cc_content, u.account AS user_name, um.account AS cc_user_name  FROM app_order_complain AS c ".
               " LEFT JOIN app_order_complain cc ON cc.parent_id=c.id ".
               " LEFT JOIN app_users u ON u.id=c.users_id ".
               " LEFT JOIN app_users um ON um.id=cc.users_id ".
               " WHERE c.id = '$where[id]' LIMIT 1";
       
        $model = M('');
        $result = $model->query($sql);
        if (isset($result[0]['id'])){
            $result = $result[0];
        }
        
        parent::set_all_time($result,array('times'));
        return $result;
    }
    
}
