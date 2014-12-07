<?php
// 订单日志模型
class OrderLogModel extends MediaBaseModel 
{
	/**
     * 添加订单日志
     * 
     * @author bumtime
     * @date   2014-10-11
     * @return bool
     */
	public function orderLogAdd($arry)
	{
		return $this->add($arry);
	}
}