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
<<<<<<< HEAD
                    $mtype = 'weibo';
                    $GeneralizeAccount = D('GeneralizeAccount');
                    $Generalize        = D('GeneralizeOrder');
                    break;
                case '2':
                    $type = '4';
                    $mtype = 'weixin';
                    $GeneralizeAccount = D('GeneralizeWeixinAccount');
                    $Generalize        = D('GeneralizeWeixinOrder');
                    break;
                case '1':
                    $type = '1';
                    $mtype = 'news';
                    $GeneralizeAccount = D('GeneralizeNewsAccount');
                    $Generalize        = D('GeneralizeNewsOrder');
                    break;
            }
            //更改子订单状态
            $status = $this->_post('status');
            $GeneralizeAccount->where(array('id' => $info['ddid']))->save(array('audit_status' => $status));
            //订单状态更改
            $status = '5';
            $Generalize->where(array('id' => $info['order_id']))->save(array('status' => $status));
            
=======
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
>>>>>>> 新增投诉流程add by chenchao
            //记录订单处理LOG
            $orderlog = D('OrderLog');
            $orderlog->content = '投诉处理，更改订单状态';
            $orderlog->add_order_log($this->user_id, $info['order_id'], $type);
<<<<<<< HEAD
            //资金处理
            //读取订单信息
            $order = $Generalize->where(array('id' => $info['order_id']))->find();
            //读取子订单信息
            $orderinfo = $GeneralizeAccount->where(array('id' => $info['ddid']))->find();

            //订单信息不全，提示操作失败
            if (!isset($order['users_id']) || !isset($orderinfo['price']) || !isset($info['order_id'])){
                $this->error('订单信息不全，处理失败！');
            }else{
                $advertisement = D('UserAdvertisement');
                $money = getAdMoney($orderinfo['price'], $mtype, $orderinfo['rebate']);
                if ($status == '13') {
                    //返还资金到广告主
                    $advertisement->setMoney($money, $order['users_id']);
                }else{
                    //扣广告主的钱
                    $advertisement->setXFMoney($money, $order['users_id']);
                    //打款给媒体主
                    $Media = D('UserMedia');
                    $Media->insertPirce($orderinfo['users_id'], $orderinfo['price'], $info['media_type'], $info['order_id']);
                }
            }
=======
            
            //退款给广告主,待处理
            if ($status == '13') {
                
            }else{
                //打款给媒体主
            }
            
            
>>>>>>> 新增投诉流程add by chenchao

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
