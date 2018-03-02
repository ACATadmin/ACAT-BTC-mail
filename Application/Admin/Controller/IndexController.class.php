<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $this->meta_title = '管理首页';
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->display();
    }
    
    
    //获取年销售额
    public function getYearView($year,$aim_type){
        $year_arr=array();
        for($i=01;$i<=12;$i++){
            $month=$i<10?'0'.$i:$i;
            $string=$year.'-'.$month.'-01';
            $BeginDate= $string;
            
            $BeginStamp=strtotime($BeginDate);
            $EndStamp=strtotime("$BeginDate +1 month -1 day")+C('DAY');
            
            
            $map['status']=1;
            $map['create_time']=array('between',$BeginStamp.','.$EndStamp);
            $map['aim_type']=$aim_type;
            
            
            $view_count=D('ViewLog')->where($map)->sum('view_num');
            $view_count=$view_count?$view_count:0;
            

            $year_arr[]=$view_count;
        }

        return $year_arr;
    }
    
    //获取X轴数值
    public function getXnum($max_count){
        $return_max = 240;
        $return_every = 40;
        if($max_count>240){
            
            if( $max_count%8!=0 ){
                for($i=1;$i<=8;$i++){
                    if( ($max_count+$i)%8==0 ){
                        $max_count=$max_count+$i;
                        break;
                    }
                }
            }
            
            $return_max = $max_count;
            $return_every = intval($return_max/8);
        }
        
        return array('max_count'=>$return_max,'every'=>$return_every);
    }
    
    
    
    public function getApiDatas($mcv,$parameter){
        $parameter['request_time'] = time();
	$parameter['appid'] = '10012';
	$parameter['appkey'] = 'NGQ2MTFmNzI2ZTY4MmVlMw==';
	$parameter['access_token'] = md5( $parameter['appid'] . $parameter['request_time'] . $parameter['appkey'] );
        
        
        $param='';
        foreach($parameter as $k=>$v){
            $param.='/'.$k.'/'.$v;
        }
        
        
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$mcv.$param;
        
	$dataJson = file_get_contents($url);
        
	//vd($parameter);echo $url;vde($dataJson);
        //vde($dataJson);
        
	$dataArr = json_decode($dataJson,true);
        if($dataArr['errcode']>0 || $dataArr['errcode']==-1){
		if( $onlyRet )return array();
		return $dataArr;
	}else{
		return $dataArr['results'];
	}
    }
    
    
    
    
    
    

}
