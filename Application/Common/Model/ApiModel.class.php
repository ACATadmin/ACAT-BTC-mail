<?php
namespace Common\Model;

class ApiModel extends BaseModel{
	
	protected $_auto = array(
		array('is_pagination','set_checked',3,'callback'),
	);
		
	public function format( $info ){
		$info['api_group_name'] 	= D('ApiGroup')->where( array('id'=>$info['api_group_id']) )->getField('api_group_name');
		return $info;
	}
	
	public function getInterfaceTree(){
		//获取接口组
		$InterfaceArr = M('Api')->where( array('status'=>1) )->field('id,api_name,api_group_id,development_status,developer')->select();
		
		$newArr = array();
		$count = count($InterfaceArr);
		
		for( $i=0;$i<$count;$i++ ){
			$api_group_id = $InterfaceArr[$i]['api_group_id'];
			if( !isset($newArr[ $InterfaceArr[$i]['api_group_id'] ]) ){
				$newArr[ $api_group_id ] = array('group_name'=>D('ApiGroup')->where( array('id'=>$api_group_id) )->getField('api_group_name'),child=>array() );
			}
			$newArr[ $api_group_id ]['child'][] = $InterfaceArr[$i];
		}
		
		
		
		return $newArr;
	}
	
	
	
	
	
	
	
	
	
	
}