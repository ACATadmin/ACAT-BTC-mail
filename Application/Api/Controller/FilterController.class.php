<?php
namespace Api\Controller;

/**
 * 消息
 * @author wei
 *
 */
class FilterController extends ApiController {
    public function getList($category_id='',$special_id=""){
        if ($special_id) {
           
            // $map['sgg.special_id']=$special_id;
            // $mysort='g.sort desc,g.id desc';
            // C('LIST_ROWS',10000);
            // $prefix   = C('DB_PREFIX');
            // $l_table  = $prefix.(GOODS_BRAND);
            // $r_table  = $prefix.(SPECIAL_GROUP_BRAND);
            // $model    = M()->table( $l_table.' g' )->join ( $r_table.' sgg ON g.id=sgg.id' );
            // $list0 = $this->lists($model,$map,$mysort,null,'g.*');
            // $list0=D('Goods')->formatList($list);
            // vde($list0);

            $map['g.special_id']=$special_id;
            $map['sgg.status']=array('neq',-1);
            $mysort='sgg.sort desc,sgg.id desc';
            // C('LIST_ROWS',10000);
            $prefix   = C('DB_PREFIX');
            $l_table  = $prefix.(SPECIAL_GROUP_GOODS);
            $r_table  = $prefix.(GOODS);
            $list=M()->table( $l_table.' g' )->join ( $r_table.' sgg ON g.id=sgg.id' )->group('brand_id')->where($map)->order($mysort)->field("sgg.brand_id")->select();
            // $info['goods_child']    = D('Goods')->formatListOne($list);
            $brand_list=getIdIndexArr($list,"brand_id");
            foreach ($brand_list as $key => $v) {
                $brand_id[]=$v['brand_id'];
            }
            $wheres['id']= array("in" , $brand_id);
            $list0=D('GoodsBrand')->where($wheres)->select();
            // vde($list0);
            $list1=array();
            $list1[0]=array('name'=>'不限','parm'=>'');
            foreach($list0 as $k=>$v){
                $has_goods=D('Goods')->where('brand_id='.$v['id'])->find();
                if($has_goods){
                    $list1[]=array('name'=>$v['brand_name'],'parm'=>$v['id']);
                }
            }
            $list2=C('PriceFilter');
            $list3=C('IntegralPriceFilter');
            $tree=D('GoodsCategory')->getTree();
            $list4=array();
            $list4[0]=array('name'=>'不限','parm'=>'');
            foreach($tree as $k=>$v){
                $list4[]=array('name'=>$v['category_name'],'parm'=>$v['id']);
            }
            $this->outputJsonData(array(
                'brand'=>$list1,
                'price'=>$list2,
                'integral'=>$list3,
                'category'=>$list4,
            ));
        }else{
            if($category_id!=''){
                $map['cate_id']=$category_id;
            }
            $list0=D('GoodsBrand')->field('id,brand_name')->where($map)->order('id desc')->select();
            $list1=array();
            $list1[0]=array('name'=>'不限','parm'=>'');
            foreach($list0 as $k=>$v){
                $has_goods=D('Goods')->where('brand_id='.$v['id'])->find();
                if($has_goods){
                    $list1[]=array('name'=>$v['brand_name'],'parm'=>$v['id']);
                }
            }
            $list2=C('PriceFilter');
            $list3=C('IntegralPriceFilter');
            $tree=D('GoodsCategory')->getTree();
            $list4=array();
            $list4[0]=array('name'=>'不限','parm'=>'');
            foreach($tree as $k=>$v){
                $list4[]=array('name'=>$v['category_name'],'parm'=>$v['id']);
            }
            $this->outputJsonData(array(
                'brand'=>$list1,
                'price'=>$list2,
                'integral'=>$list3,
                'category'=>$list4,
            ));
        }
        
    }
	
}