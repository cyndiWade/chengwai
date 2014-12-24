<?php
/**
 * 投诉或申诉
 * 在媒体主执行后，由广告主提出投诉-》子订单状态更改为投诉-》对应媒体主提出申诉-》后台处理-》订单结束
 * 
 * 1. 申诉的父ID为投诉的ID
 * 2. 记录对应订单ID、用户ID、申请理由
 * 
 *  
 * //增加投诉流程 订单状态 add by chenchao 2014-12-06
 * 11=>array(
 *	'status'=>11,
 *	'explain'=>'投诉中'
 *	),
 *  12=>array(
 *	'status'=>12,
 *	'explain'=>'申诉中'
 *	),
 * 13=>array(
 *	'status'=>13,
 *	'explain'=>'投诉中'
 *	),
 * 14=>array(
 *	'status'=>14,
 *	'explain'=>'申诉中'
 *	),
 * //增加投诉流程 订单状态 add by chenchao 2014-12-06
 * 
 * @author chenchao
 */
class OrderComplainAction extends MediaBaseAction
{
    //每个类都要重写此变量
    protected $is_check_rbac = true; //是否需要RBAC登录验证
    protected $not_check_fn = array(); //无需登录和验证rbac的方法名

    //控制器说明
    private $module_explain = '投诉';
    private $_type = '1'; //类型，默认1

    //初始化数据库连接
    protected $db = array(
        'OrderComplain' => 'OrderComplain',
        'GeneralizeAccount' => 'GeneralizeAccount',
        'GeneralizeWeixinAccount' => 'GeneralizeWeixinAccount',
        'GeneralizeNewsAccount' => 'GeneralizeNewsAccount',
        'Users' => 'Users',
    );

    //构造方法
    public function __construct()
    {
        parent::__construct();
        $this->_init_data();
    }

    //初始化需要的数据
    private function _init_data()
    {
        $this->_type = $this->_get('type'); //类型：1投诉、2申诉
    }

    /**
     * 添加投诉或申诉
     * 
     * @author chenchao
     */
    public function add_complain()
    {
        $order_id   = $this->_get('complain_order_id'); //订单ID
        $content    = $this->_get('content'); //投诉原因
        $media_type = $this->_get('media_type'); //媒体类型
        $ddid       = $this->_get('ddid'); //子订单ID
        $parent = array();
        
        if (empty($order_id) || empty($ddid)){
            $this->error('信息为空');
        }
        
        //是否已存在些订单的投诉或申诉，防止重复提交
        $where["order_id"]	= $order_id;
        $where['ddid'] = $ddid;
        $where['type'] = $this->_type;
        $count = $this->db['OrderComplain']->where($where)->count();
        if ($count > 0){
            $this->error('申诉正在处理中,不用重复提交！');
        }else{
            if ($this->_type == '1'){
                //订单投诉中状态
                $status = '11';
            }else{
                //订单申诉中状态
                $status = '12';
                //查询此订单投诉ID
                $where['type'] = '1';
                $parent = $this->db['OrderComplain']->where($where)->find();
                if (!isset($parent['id'])){
                    $this->error('没找到相关投诉！');
                }
            }
            //子订单相关模型
            switch ($media_type)
       		{
       			case '3':
       				$GeneralizeAccount	= D('GeneralizeAccount');
       				$orderModel			= M('generalize_order');
       				$tipsUrl	 = U('/Advert/WeiboOrder/generalize_detail', array('order_id'=>$order_id));
        			break;
       			case '2':
       				$GeneralizeAccount	= D('GeneralizeWeixinAccount');
       				$orderModel			= M('generalize_weixin_order');
       				$tipsUrl	 = U('/Advert/WeixinOrder/generalize_detail', array('order_id'=>$order_id));
        			break; 
       			case '1':
       				$GeneralizeAccount	= D('GeneralizeNewsAccount');
       				$orderModel			= M('generalize_news_order');
       				$tipsUrl	 = U('/Advert/News/generalize_detail', array('order_id'=>$order_id));
        			break;    			   			
       		}
       		
            //插入数据
            $arryComplain = array();
            $arryComplain['users_id']   = $this->oUser->id;
            $arryComplain['order_id']   = $order_id;
            $arryComplain['ddid']       = $ddid;
            $arryComplain['content']    = $content;
            $arryComplain['type']       = $this->_type;
            $arryComplain['media_type'] = $media_type;
            $arryComplain['times']      = time();
            //父投诉记录
            if ($this->_type == '2' && !empty($parent) && isset($parent['id'])){
                $arryComplain['parent_id']   = $parent['id'];
            }else{
                $arryComplain['parent_id']   = 0;
            }
            $res = $this->db['OrderComplain']->orderComplainAdd($arryComplain);

            
			$tipsInfo = C('MESSAGE_TYPE_MEDIA');

			$where_order['id']  = $order_id;
			$order_user = $orderModel->where($where_order)->getField('users_id');
			
			$data['send_from_id']	=	C('MESSAGE_ADMIN_ID');
			$data['send_to_id']		=	$order_user;
			$data['subject']		=	$tipsInfo[10]['subject'];
			$data['content']		=	sprintf($tipsInfo[10]['content'], $tipsUrl);
			$data['message_time']	=	time();

			if($data)
				parent::sendMessageInfo($data, 1);
			
            
            if ($res === false) {
                $this->error('处理失败，请重试！');
            } else {
                //更新子订单状态
                $where['id'] = $ddid;
    		    $data['audit_status'] = $status;
    		    $GeneralizeAccount->where($where)->save($data);
                $this->success('处理成功，请耐心等待处理！');
            }
        }
    }
}

?>