<?php

namespace Admin\Widget;
use Think\Action;

/**
 * 通用widget
 */

class ViewWidget extends Action{
	
	public static $tempBasePath = '../Widget/View/View/';
	public function display($temp){
		parent::display(self::$tempBasePath.$temp);
	}
	
	//社保类型头
	public function insuranceTypeTh($colspan='2'){
		$this->colspan = $colspan;
		$this->display('insuranceTypeTh');
	}
	//社保类型头-单位/个人
	public function insuranceTypeTypeTd(){
		$this->display('insuranceTypeTypeTd');
	}
	
	
}
