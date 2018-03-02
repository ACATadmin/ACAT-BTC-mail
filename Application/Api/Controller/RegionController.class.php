<?php
namespace Api\Controller;
use User\Api\RegionApi;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegionController
 *
 * @author Administrator
 */
class RegionController extends ApiController{
    public function getRecommendCity(){
            $Model = D('Region');
            $where=array('hot'=>1);
            $order='sort desc ';
            $list = $this->setModelListField( $Model )->lists ( $Model,$where,$order);
            //$list = $Model->formatCollectList($list);
            $this->outputJsonData(array(
			'pageInfo'	=> $this->getPageInfo(),
			'dataset'	=> $list
            ));
    }

    public function getCityList($parent_id,$region_type){
        $parent_id=$parent_id?$parent_id:1;
        $region_type=$region_type?$region_type:1;
        
        if( in_array($parent_id,array('2','3','10')) ){
            $parent_id = D('Region')->where( array('parent_id'=>$parent_id) )->getField('region_id');
            $region_type = 3;
        }
        
        
        //获取数据
        $arr = D('Region')->getRegion( array('parent_id'=>$parent_id,'region_type'=>$region_type) );
        $this->outputJsonData($arr);
    }
    //获取快递公司
    public function getShippingcompanyList(){
        $list=D('ShippingCompany')->field('id as shipping_id,company_name as shipping_company')->where(array('status'=>1))->select();
        $this->outputJsonData($list);
    }
}
