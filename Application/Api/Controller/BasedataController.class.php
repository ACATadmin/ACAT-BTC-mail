<?php
namespace Api\Controller;
use OT\DataDictionary;

/**
 * 基础信息
 */
class BasedataController extends ApiController {


	/**
	 * 获取当前平台支持/推荐的城市切换列表
	 * @param unknown $type
	 */
	public function getCityList($type){
	
	}
	
	
	
	
	
	
	
	
	
	
	//以下部分仅供后台使用接口文档上不体现
	//================================================省市联动==================================================
	public function selRegion(){
		
		$region_type = $_GET['region_type'];
		$parent_id	=$_GET['parent_id'];
		//获取数据
		$arr = D('Region')->getRegion( array('parent_id'=>$_GET['parent_id'],'region_type'=>$region_type) );
		 
		if( $region_type==0 ){
			$region = '国家';
		}elseif( $region_type==1 ){
			if($parent_id==36){
				$region = '国家';
			}else{
				$region = '省';
			}
			
		}elseif( $region_type==2 ){
			$region = '市';
		}elseif( $region_type==3 ){
			$region = '地区';
		}else{
			$region = '...';
		}
		$frist_info='<option value="0">请选择'.$region.'</option>';
		//vde($arr);
		//打印数据
		echo $this->selRegionHtml($arr,$frist_info);
	}
	//生成省市联动HTML
    private function selRegionHtml($arr,$frist_info='<option value="0">请选择...</option>'){
    	
    	$str = $frist_info;
    	
    	$count = count($arr);
    	for($i=0;$i<$count;$i++){
    		$checked = '';
    		$str .= '<option value="'.$arr[$i]['region_id'].'">'.$arr[$i]['region_name'].'</option>';
    	}
    	
    	return $str;
    }
    
   

}