<?php
namespace Common\Model;
use Think\Model;
/**
 *基类模型
 */
class BaseModel extends Model{
	public $getListFields 		= '*';
	public $getListFieldsExcept 	= false;
	/*
	//单条数据格式化，子类继承使用
	public function format( $info ){
		$info['pack_name'] = $info['pack_name'].'944499';
		return $info;
	}
	*/
	
	//包装列表格式化
	public function formatList( $list,$adv=0 ){
		//如果需要格式化数据，逐条格式化
		if( method_exists($this,'format') ){
			for($i=0;$i<count($list);$i++){
				if( $list[$i] ){
					$list[$i] = $this->format( $list[$i], $adv );
				}
			}
		}
		return $list;
	}
	
	//自定义列表格式化
	public function formatListCustom( $list,$custom_func,$type="" ){
		//如果需要格式化数据，逐条格式化
		if( $type == 'function' ){	//函数式回调
			if (function_exists($custom_func)) {
				for($i=0;$i<count($list);$i++){
					if( $list[$i] ){
						$list[$i] = $custom_func( $list[$i] );
					}
				}
			}
		}else{						//当前方法中回调
			if( method_exists($this,$custom_func) ){
				for($i=0;$i<count($list);$i++){
					if( $list[$i] ){
						$list[$i] = $this->$custom_func( $list[$i] );
					}
				}
			}
		}
		
		return $list;
	}
	
	/**
	 * 将列表数据中是否已关注/收藏目标标识出来
	 * @param unknown $list
	 * @param unknown $uid	
	 * @param unknown $type
	 * 						1	音乐人
	 * 						2	场馆
	 * 						3	潮人说
	 * 						4	演出
	 * @param unknown $relevance_type
	 * 						1	关注收藏
	 * 						2	点赞
	 * @param string $aimIdName
	 * @return unknown
	 */
	public function formatCollectList($list,$uid,$type,$relevance_type,$aimIdName='id'){
		//获取当前列表中所有目标ID
		$aimIds = getIdArr($list,$aimIdName);
		
		//获取关注收藏表中对应用户对应目标数组所有的记录
		$aim_ids = array();
		if( $uid ){
			$map = array(
					'uid'				=> $uid,
					'type'				=> $type,
					'relevance_type'	=> $relevance_type
			);
			$aim_ids = D('FocusCollect')->where( $map )->field('aim_id')->select();
			$aim_ids = getIdArr($aim_ids,'aim_id');
		}
		
		$listCount = count($list);
		$fieldName = ($relevance_type==1)?'is_collect':'is_zan';
				
		for ($i=0;$i<$listCount;$i++){
			if( in_array( $list[$i][$aimIdName],$aim_ids ) ){
				$list[$i][$fieldName] = 1;
			}else{
				$list[$i][$fieldName] = 0;
			}
		}
		
		return $list;
	}
	
	//获取指定记录详情
	public function getInfo( $map ){
		
		if( is_array($map) ){		//数组查询
			$info = $this->where( $map )->find();
		}else{						//主键检索
			$Pk = $this->getPk();
			$info = $this->where( array($Pk=>$map) )->find();
		}
				
		//格式化
		if( !empty($info) && method_exists($this,'format') ){
			$info = $this->format( $info );
		}
		
				
		return $info;
	}
	
	//获取列表
	public function getList( $map ,$order=""){		
		$list = $this->where( $map )->order( $order )->field( $this->getListFields, $this->getListFieldsExcept )->select();
		return $this->formatList( $list );
	}
	
	//自动完成完成，设置checked的默认值
	public function set_checked( $is_checked ){
		return $is_checked?$is_checked:0;
	}
	
	public function combination($model,$map,$order,$base,$field)
	{
		$data['model'] = $model;
		$data['map'] = $map;
		$data['order'] = $order;
		$data['base'] = $base;
		$data['field'] = $field;
		return $data;
	}
}