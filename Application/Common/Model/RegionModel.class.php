<?php
namespace Common\Model;
use Think\Model;

class RegionModel extends Model{

	public function __construct() {
		parent::__construct();
	}
	
	
	//获取全部
	public function getAll(){
		return $this->select();
	}
	
	//实时获取地区数据
	public function getRegion( $map = '' ){
		return $this->where( $map )->order('sort desc,region_id asc')->select();		
	}
	
	//获取省
	public function get_province(){
		
		return $this->where( array('region_type'=>1) )->select();		
	}
	
	//获取市
	public function get_city($parent_id){

		return $this->where( array('region_type'=>2,'parent_id'=>$parent_id) )->select();		
	}	
	
	//获取区
	public function get_area($parent_id){

		return $this->where( array('region_type'=>3,'parent_id'=>$parent_id) )->select();		
	}
		
	//获取指定区域ID
	public function getRegionId($hotel_cs,$region_type=2){
		$map['region_name'] = array('like',$hotel_cs);
		$map['region_type'] = $region_type;
		$res = $this->field('region_id')->where( $map )->find();
		
		//vde( $res );
		return $res['region_id'];
	}
	
	//判断当前区域是否包含子类
	public function hasChild( $region_id ){
		return $this->where( array('parent_id'=>$region_id) )->count();
	}
	
	public function getName( $region_id ){
		$res = $this->field('region_name')->where(array('region_id'=>$region_id))->find();
		
		return (!empty($res['region_name']))?$res['region_name']:'';
	}
	
	//操作型
	//---------------------------------
	public function del( $region_id ){
		$bool = $this->where( array('region_id'=>$region_id) )->delete();
		return $bool;
	}

	//获取id与区域的对应数组
	public function getIdInfoArr( $idArr ){
		$idInfoArr = $this->where($idArr)->select();
		$idInfoArr = getIdIndexArr($idInfoArr,'region_id');
		
		return $idInfoArr;
	}
        public function getRegionInfo($id = 0){
		if($id)
		{
			$map['region_id'] = $id;
			$info = $this->where($map)->find();
			return $this->format($info);
		}
		else
		{
			return null;
		}
	}
	
        //格式化数组
	public function formatList($list){
		if(!$list) return $list;
		for ($i=0; $i <count($list) ; $i++) {
			$list[$i] = $this->format($list[$i]);
		}
		return $list;
	
	}
        public function format($info)
	{		
		$map['parent_id'] = $info['region_id'];
		$info['next'] = $this->where($map)->count();
		$info['region_pic_1'] = $info['region_pic'];
        $info['region_pic'] 	= ($info['region_pic'])?getImgUrl($info['region_pic']):'';
		return $info;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}