<?php
/**
 * 后台投诉管理
 * 
 * @author chenchao
 */
class OrderComplainAction extends AdminBaseAction
{
    //控制器说明
    private $module_name = '投诉申诉管理';

    //初始化数据库连接
    protected $db = array(
        //投诉
        'OrderComplain' => 'OrderComplain',
        'Users' => 'Users', //用户账号表
        'AccountNews' => 'AccountNews',
        'AccountWeibo' => 'AccountWeibo',
        'AccountWeixin' => 'AccountWeixin',
        'OrderLog'=>'OrderLog',
        );

    private $_action_name = '投诉列表';
    private $_title_name = '投诉列表';

    /**
     * 构造方法
     */
    public function __construct()
    {
        parent::__construct();
        parent::global_tpl_view(array('module_name' => $this->module_name));
    }

    /**
     * 投诉列表
     * 
     * @author chenchao
     */
    public function complain_list()
    {
        $OrderComplain = $this->db['OrderComplain'];
        //只显示投诉，每个申诉都有一个投诉
        $list = $OrderComplain->getOrderComplainList(array('type'=>'1'));

        $data['list'] = $list;
        parent::global_tpl_view(array(
            'action_name' => $this->_action_name,
            'title_name' => $this->_title_name,
            ));

        parent::data_to_view($data);
        $this->display();
    }

    /**
     * 投诉处理
     * 
     * @author chenchao
     */
    public function complain_edit()
    {
        $id = $this->_get('id');
        $OrderComplain = $this->db['OrderComplain'];
        $where['id'] = $id;
        $info = $OrderComplain->getOrderComplainInfo($where);

        if ($this->isPost() && !empty($info)) {
            //修改申诉处理状态为已处理
            $OrderComplain->where($where)->save(array('status' => '1'));
            //修改订单状态
            switch ($info['media_type']) {
                case '3':
                    $type = '2'; //日志类型
                    $GeneralizeAccount = D('GeneralizeAccount');
                    break;
                case '2':
                    $type = '4';
                    $GeneralizeAccount = D('GeneralizeWeixinAccount');
                    break;
                case '1':
                    $type = '1';
                    $GeneralizeAccount = D('GeneralizeNewsAccount');
                    break;
            }
            $status = $this->_post('status');
            $GeneralizeAccount->where(array('ddid' => $info['ddid']))->save(array('status' => $status));
            //记录订单处理LOG
            $orderlog = D('OrderLog');
            $orderlog->content = '投诉处理，更改订单状态';
            $orderlog->add_order_log($this->user_id, $info['order_id'], $type);
            
            //退款给广告主,待处理
            if ($status == '13') {
                
            }else{
                //打款给媒体主
            }
            
            

            //处理后跳转列表页
            $this->success('处理成功！', '/Admin/OrderComplain/complain_list.html');
            exit;
        }

        parent::global_tpl_view(array(
            'action_name' => '投诉处理',
            'title_name' => $this->_title_name,
            ));

        parent::data_to_view($info);
        $this->display();
    }

}
