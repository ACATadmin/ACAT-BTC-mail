<?php

namespace Admin\Widget;
use Think\Action;

/**
 * 通用widget
 */

class CommonWidget extends Action{
	
	public static $tempBasePath = '../Widget/View/Common/';
	public function display($temp){
		parent::display(self::$tempBasePath.$temp);
	}
	
	//列表页下拉选择控件
	public function searchFormdropDown($dataList,$currentVal,$searchKeyName,$idName='id',$nameName='name',$ulWidth=''){
		
		$this->assign('dataList', $dataList);
		$this->assign('currentVal', $currentVal);
		$this->assign('searchKeyName', $searchKeyName);
		$this->assign('idName', $idName);
		$this->assign('nameName', $nameName);
		$this->assign('ulWidth', $ulWidth);
		
		//vd($dataList);vd($currentVal);
		
		$this->display('searchFormdropDown');
	}
	
	
	//
	public function selectMusicianList( $submit_input_name='',$submit_input_val='' ){
		
		$rand = rand(10000, 99999);
		
		$this->assign('target',"{target:'#select_musician_modal".$rand."'}");
		$this->assign('select_musician_modal_id','select_musician_modal'.$rand);
		$this->assign('select_musician_modal_id_form','select_musician_modal_id_form'.$rand);
		
		
		$this->assign('submit_input_name',$submit_input_name);
		$this->assign('submit_input_val',$submit_input_val);
		
		//已设置的艺人
		//$submit_input_arr = explode(',', trim($submit_input_val,','));
		$musicianArr = D('Musician')->where( array('id'=>array('in',trim($submit_input_val,',')) ) )->field('id,musician_name')->select();
		//vd( $musicianArr );
		
		$this->assign('musicianArr',$musicianArr);
		$this->display('selectMusicianList');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
