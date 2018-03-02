<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}
function int_to_string1(&$data) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        if($row['check_status']==0){//0待审核，1审核通过，2审核失败
            $data[$key]['status_text'] = '待审核';
        }else if($row['check_status']==1){
            switch($row['status']){//1即将开始  2正在进行 3已结束 -1已删除
                case 1:
                    $data[$key]['status_text'] = '即将开始';
                    break;
                case 2:
                    $data[$key]['status_text'] = '订货进行中';
                    break;
                case 3:
                     $data[$key]['status_text']='已结算';
                    break;
            }
        }else if($row['check_status']==2){
            $data[$key]['status_text'] = '审核不通过';
        }
    }
    return $data;
}
function int_to_string1new(&$data) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        if($row['check_status']==0){//0待审核，1审核通过，2审核失败
            $data[$key]['status_text'] = '待审核';
        }else if($row['check_status']==1){
            if(!$row['goods_platform_id']){
                if($row['status']==3){
                    $data[$key]['status_text'] = '订货会已结束';
                    if($row['get_shipping_status']==2){
                        if($row['back_shipping_status']==0){
                            $data[$key]['status_text'] = '待退样品';
                        }else if($row['back_shipping_status']==1){
                            $data[$key]['status_text'] = '待收样品';
                        }else if($row['back_shipping_status']==2){
                            $data[$key]['status_text'] = '已收样品';
                        }
                    }
                }else{
                    if($row['get_shipping_status']==0){
                        $data[$key]['status_text'] = '待寄样品';
                    }else if($row['get_shipping_status']==1){
                        $data[$key]['status_text'] = '待平台收货';
                    }else if($row['get_shipping_status']==2){
                        $data[$key]['status_text'] = '待发布';
                    }
                }
            }else{
                switch($row['status']){//1即将开始  2正在进行 3已结束 -1已删除
                    case 1:
                        if($row['is_sale']==0){
                            $data[$key]['status_text'] = '未上架';
                        }else{
                            $data[$key]['status_text'] = '即将开始';
                        }
                        break;
                    case 2:
                        if($row['is_sale']==0){
                            $data[$key]['status_text'] = '未上架';
                        }else{
                            $data[$key]['status_text'] = '订货进行中';
                        }
                        break;
                    case 3:
                        //判断是否有购买商品
                        $has_order=D('GoodsPlatform')->checkHasOrder($row['goods_platform_id']);
                        //
                        if(!$has_order){
                            if($row['get_shipping_status']==2){
                                if($row['back_shipping_status']==0){
                                    $data[$key]['status_text'] = '待退样品';
                                }else if($row['back_shipping_status']==1){
                                    $data[$key]['status_text'] = '待收样品';
                                }else if($row['back_shipping_status']==2){
                                    $data[$key]['status_text'] = '已收样品';
                                }
                            }
                        }else{
                            if($row['produce_status']==0){
                                $data[$key]['status_text'] = '待生产';
                            }else if($row['produce_status']==1){
                                $data[$key]['status_text'] = '生产中';
                            }else if($row['pay_designer_status']==0){
                                $data[$key]['status_text'] = '待结算';
                            }else if($row['pay_designer_status']==1){
                                $data[$key]['status_text'] = '已结算';
                            }
                        }
                        break;
                }
            }
        }else if($row['check_status']==2){
            $data[$key]['status_text'] = '审核不通过';
        }
    }
    return $data;
}

function int_to_string2(&$data) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        if($row['comfirm_status']==0){//0 未确认 1已确认 2确认失败（无货）
            $data[$key]['status_txt'] = '待处理';
        }else if($row['comfirm_status']==1 && $row['pay_status']==0){
            if($row['status']==-1){
                $data[$key]['status_txt'] = '已取消';
            }else{
                $data[$key]['status_txt'] = '待付款';
            }
        }else if($row['comfirm_status']==1 && $row['pay_status']==2){
            switch($row['shipping_status']){//订单状态。0，未付款；1，未发货；2，已发货；3，已完成；
                case 0:
                    $data[$key]['status_txt'] = '待发货';
                    break;
                case 1:
                    $data[$key]['status_txt'] = '已发货';
                    break;
                case 2:
                    $data[$key]['status_txt']='已完成';
                    break;
            }
        }else if($row['comfirm_status']==2){
            $data[$key]['status_txt'] = '已拒绝';
        }
        $buyerinfo=M('Member')->field('nickname')->find($row['source_id']);
        $data[$key]['source_name']=$buyerinfo['nickname'];
        $data[$key]['goods_amount_exp']=C('PriceFuHao').$row['goods_amount'];
//        $data[$key]['goods_number']=count($row['goods_list']);
    }
    return $data;
}
function int_to_onlinestring(&$data,$map=array('online'=>array(1=>'在售',0=>'下架'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}
/**
 *
 *
 *  生成需要的js循环。递归调用	PHP
 *
 *  形式参考 （ 2个规格）
 *  $('input[type="checkbox"]').click(function(){
 *      str = '';
 *      for (var i=0; i<spec_group_checked[0].length; i++ ){
 *      td_1 = spec_group_checked[0][i];
 *          for (var j=0; j<spec_group_checked[1].length; j++){
 *              td_2 = spec_group_checked[1][j];
 *              str += '<tr><td>'+td_1[0]+'</td><td>'+td_2[0]+'</td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td>';
 *          }
 *      }
 *      $('table[class="spec_table"] > tbody').empty().html(str);
 *  });
 */

function recursionSpec($len=0,$list){
    $sign=count($list);
    $fordd='';
    if($len < $sign){
        $fordd.= "for (var i_".$len."=0; i_".$len."<spec_group_checked[".$len."].length; i_".$len."++){td_".(intval($len)+1)." = spec_group_checked[".$len."][i_".$len."];\n";
        $len++;
        recursionSpec($len,$sign);
    }else{
        $fordd.= "var tmp_spec_td = new Array();\n";
        for($i=0; $i< $len; $i++){
            $fordd.= "tmp_spec_td[".($i)."] = td_".($i+1)."[1];\n";
        }
        $fordd.= "tmp_spec_td.sort(function(a,b){return a-b});\n";
        $fordd.= "var spec_bunch = 'i_';\n";
        for($i=0; $i< $len; $i++){
            $fordd.= "spec_bunch += tmp_spec_td[".($i)."];\n";
        }
        $fordd.= "str += '<input type=\"hidden\" name=\"spec['+spec_bunch+'][goods_id]\" nc_type=\"'+spec_bunch+'|id\" value=\"\" />';";
        for($i=0; $i< $len; $i++){
           $fordd.= "if (td_".($i+1)."[2] != null) { str += '<input type=\"hidden\" name=\"spec['+spec_bunch+'][color]\" value=\"'+td_".($i+1)."[1]+'\" />';}";
            echo "str +='<td><input type=\"hidden\" name=\"spec['+spec_bunch+'][sp_value]['+td_".($i+1)."[1]+']\" value=\"'+td_".($i+1)."[0]+'\" />'+td_".($i+1)."[0]+'</td>';\n";
        }
        $fordd.= "str +='<td><input class=\"text price\" type=\"text\" name=\"spec['+spec_bunch+'][price]\" data_type=\"price\" nc_type=\"'+spec_bunch+'|price\" value=\"\" /><em class=\"add-on\"><i class=\"icon-renminbi\"></i></em></td><td><input class=\"text stock\" type=\"text\" name=\"spec['+spec_bunch+'][stock]\" data_type=\"stock\" nc_type=\"'+spec_bunch+'|stock\" value=\"\" /></td><td><input class=\"text sku\" type=\"text\" name=\"spec['+spec_bunch+'][sku]\" nc_type=\"'+spec_bunch+'|sku\" value=\"\" /></td></tr>';\n";
        for($i=0; $i< $len; $i++){
            $fordd.= "}\n";
        }
    }
    return $fordd;
}
/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}