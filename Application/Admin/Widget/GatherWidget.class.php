<?php

namespace Admin\Widget;
use Think\Action;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class GatherWidget extends Action{
	
	public function index($gather_uid){
		$this->gather_uid = $gather_uid;
		$this->gather_user_list = D('AuthGroupAccess')->getGatherUserList();
		$this->display('../Widget/View/adminGatherSelect');
	}
	
}
