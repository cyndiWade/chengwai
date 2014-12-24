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
        	
        	 $status = $this->_post('status');
        	 $cStatus = ($status == '13') ? 1 : 2;
            //修改申诉处理状态为已处理
            $OrderComplain->where($where)->save(array('status' => $cStatus));
            //修改订单状态
            switch ($info['media_type']) {
                case '3':
                    $type = '2'; //日志类型
                    $mtype = 'weibo';
                    $GeneralizeAccount = D('GeneralizeAccount');
                    $Generalize        = D('GeneralizeOrder');
                    $tipsUrl 		   = U('/Media/EventOrder/show', array('id'=>$info['order_id']));
                    $tipsAdUrl		   = U('/Advert/WeiboOrder/generalize_detail', array('order_id'=>$info['order_id']));  
                    break;
                case '2':
                    $type = '4';
                    $mtype = 'weixin';
                    $GeneralizeAccount = D('GeneralizeWeixinAccount');
                    $Generalize        = D('GeneralizeWeixinOrder');
                    $tipsUrl 		   = U('/Media/EventOrder/showWeixin', array('id'=>$info['order_id']));
                    $tipsAdUrl		   = U('/Advert/WeixinOrder/generalize_detail', array('order_id'=>$info['order_id']));
                    break;
                case '1':
                    $type = '1';
                    $mtype = 'news';
                    $GeneralizeAccount = D('GeneralizeNewsAccount');
                    $Generalize        = D('GeneralizeNewsOrder');
                    $tipsUrl 		   = U('/Media/EventOrder/showNews', array('id'=>$info['order_id']));
                    $tipsAdUrl		   = U('/Advert/News/generalize_detail', array('order_id'=>$info['order_id']));
                    break;
            }
            //更改子订单状态

            $GeneralizeAccount->where(array('id' => $info['ddid']))->save(array('audit_status' => $status));
            //订单状态更改
            $order_status = '5';
            $Generalize->where(array('id' => $info['order_id']))->save(array('status' => $order_status));
            
            //记录订单处理LOG
            $orderlog = D('OrderLog');
            $orderlog->content = '投诉处理，更改订单状态';
            $orderlog->add_order_log($this->user_id, $info['order_id'], $type);
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
                    $advertisement->setMoney($money, $order['users_id'], 2, $info['media_type'], $info['order_id']);
                    
    				//申诉失败发站内短信 add by bumtime 20141223
	    		    $tipsInfo = C('MESSAGE_TYPE_ADMIN');
	    		    $messageData[0]['send_from_id']		=	$this->oUser->id;
					$messageData[0]['send_to_id']		=	$orderinfo['users_id'];
					$messageData[0]['subject']			=	$tipsInfo[2]['subject'];
					$messageData[0]['content']			=	sprintf($tipsInfo[2]['content'], $tipsUrl);
					$messageData[0]['message_time']		=	time();
					
					//投诉成功发站内短信(to 广告主) add by bumtime 20141223
	    		    $messageData[1]['send_from_id']		=	$this->oUser->id;
					$messageData[1]['send_to_id']		=	$order['users_id'];
					$messageData[1]['subject']			=	$tipsInfo[3]['subject'];
					$messageData[1]['content']			=	sprintf($tipsInfo[3]['content'], $tipsAdUrl);
					$messageData[1]['message_time']		=	time();
					
					
	
					parent::sendMessageInfo($messageData, 2);
				
                }else{
                    //扣广告主的钱
                    $advertisement->setXFMoney($money, $order['users_id'], $info['media_type'], $info['order_id']);
                    //打款给媒体主
                    $Media = D('UserMedia');
                    $Media->insertPirce($orderinfo['users_id'], $orderinfo['price'], $info['media_type'], $info['order_id']);
   
                    //申诉成功发站内短信 add by bumtime 20141223
	    		    $tipsInfo = C('MESSAGE_TYPE_ADMIN');
	    		    $messageData[0]['send_from_id']		=	$this->oUser->id;
					$messageData[0]['send_to_id']		=	$orderinfo['users_id'];
					$messageData[0]['subject']			=	$tipsInfo[4]['subject'];
					$messageData[0]['content']			=	sprintf($tipsInfo[4]['content'], $tipsUrl);
					$messageData[0]['message_time']		=	time();
					
					
					//投诉失败发站内短信(to 广告主) add by bumtime 20141223
	    		    $messageData[1]['send_from_id']		=	$this->oUser->id;
					$messageData[1]['send_to_id']		=	$order['users_id'];
					$messageData[1]['subject']			=	$tipsInfo[5]['subject'];
					$messageData[1]['content']			=	sprintf($tipsInfo[5]['content'], $tipsAdUrl);
					$messageData[1]['message_time']		=	time();
	
					parent::sendMessageInfo($messageData, 2);
					
                }
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
