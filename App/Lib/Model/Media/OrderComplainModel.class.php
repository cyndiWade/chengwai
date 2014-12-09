<?php
// 投诉
class OrderComplainModel extends MediaBaseModel 
{
	/**
     * 添加投诉
     * @author chenchao
     * @date   2014-12-06
     * @return bool
     */
	public function orderComplainAdd($arry)
	{
		return $this->add($arry);
	}
}