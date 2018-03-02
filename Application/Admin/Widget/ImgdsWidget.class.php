<?php

namespace Admin\Widget;
use Think\Action;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class ImgdsWidget extends Action{
	
	public function init($picKey,$picName,$picId,$suggestSize){
		
		$this->assign('picKey', $picKey);
		$this->assign('picName', $picName);		
		// if(strstr($picId,",")){
		// 	$picIds = explode(',', $picId);
		// 	$this->assign('picIds', $picIds);
		// }else{
		// 	$this->assign('picId', $picId);
		// }
		$this->assign('picId', $picId);
		$suggestSizes = explode('*', $suggestSize);
		$this->assign('suggestSizes', $suggestSizes);
		$this->assign('suggestSize', $suggestSize);
		
		$this->display('../Widget/View/adminImgdsUpload');
	}
	
	

	
}
