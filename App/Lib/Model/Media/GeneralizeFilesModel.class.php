<?php
/**
 * 活动订单下文件类
 * 
 */
class GeneralizeFilesModel extends MediaBaseModel 
{
	/**
     * 保存上传截图
     * 
     * @param  array 	$arryInfo 保存数组
     * 
     * @author bumtime
     * @date   2014-10-04

     * @return bool	
     */
    public function addFinish($arryInfo)
    {
    	$users_id_new = $this->add($arryInfo);
    	return $users_id_new;
    }
}