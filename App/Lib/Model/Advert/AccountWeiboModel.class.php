<?php
	//微博数据
	class AccountWeiboModel extends AdvertBaseModel
	{

		//获得总数和直取10条
		public function getListTen()
		{
			$count = $this->count();
			$list = $this->limit('10')->select();
			//$returnArray = array('count'=>$count,'list'=>$this->setList($list));
			$returnArray = array('count'=>$count,'list'=>$list);
			return $returnArray;
		}

		//生成HTML静态文件
		private function setList($array)
		{
			$str = '';
			foreach($array as $value)
			{
				$str .= '<tr>';
				$str .= '<td class="t1">';
				$str .= '<div class="weibopart"><input type="checkbox" class="check" />';
				$str .= 	'<img src="" class="wbimg" />';
				$str .= 		'<div class="grp fl">';
				$str .= 		'<h5 class="l"><b>全球头条新闻</b><span class="heart"></span><span class="heart"></span></h5>';
				$str .= 		'<div class="address l">'.$value['account_name'].'</div>';
				$str .= 		'<div class="desc l">简介：全球头条新闻全球头条新闻</div>';
				$str .= 		'</div>';
				$str .= '</div>';
				$str .= '</td>';
				$str .= '<td class="t2"><b class="red">'.$value['fans_num'].'万</b></td>';
				$str .= '<td class="t3">1.00</td>';
				$str .= '<td class="t4">'.$value['yg_zhuanfa'].'元</td>';
				$str .= '<td class="t5">'.$value['rg_zhuanfa'].'元</td>';
				$str .= '<td class="t6">'.$value['yg_zhifa'].'元</td>';
				$str .= '<td class="t7">'.$value['rg_zhuanfa'].'元</td>';
				$str .= '<td class="t8">'.$value['week_order_num'].'</td>';
				$str .= '<td class="t9"><div class="sex"><p>男：占20%</p><p>女：占20%</p></div></td>';
				$str .= '<td class="t10"><div class="date"><p class="select">09月10日</p><p>09月11日</p><p>09月12日</p></div></td>';
				$str .= '<td class="last"><div class="ctrl"><span>详情</span><span>收藏</span><span>拉黑</span></div></td>';
			  	$str .= '</tr>';
			}
       	 	//$json_str_clear = str_replace('"', "'", $str);
        	$json_str_clear = preg_replace("'([\s])[\s]+'", '', $json_str_clear);	
			return $str;
		}
	}