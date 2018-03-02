<?php
namespace Api\Controller;
use Common\Controller\CombaseController;
/**
 * Api公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class ApiController extends CombaseController {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		echo 404;
	}

    protected function _initialize(){
    	parent::_initialize();
        
        
    	
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }        
//        C('LIST_ROWS',2);
        
        
        //当前接口访问操作
        $this->apiVerification();
        
        
        
        //定时任务
//        endNoticeLive();
//        liveStartSoon();
        //
        
        //接口开始执行时间
        G('interface_begin');	
    }

     //Api接口访问校验，及数据记载
    private function apiVerification111(){
        if( CONTROLLER_NAME == 'Index' ){
            return true;
        }
        
        
        //接口请求次数增加
        $api_url = CONTROLLER_NAME .'/'. ACTION_NAME;
        if($api_url!='User/sendverifycode')
        D('Api')->where( array('api_url'=>$api_url) )->setInc('access_num');
        $time = time();
        //其他限制
        //对用户相关操作需要验证用户user_token，以保证当前对用户的操作或取用户的数据为当前用户本身（用户间相互访问）
        $APP_AUTH      = C('APP_AUTH');
        $appid          = I('appid');
        $request_time   = I('request_time');
        $access_token   = I('access_token');
//        if( ($api_url=='User/sendverifycode') ){   //开发期间，不传不限制，传了才验证
            
            if( !isset($APP_AUTH[$appid]) || empty($APP_AUTH[$appid]) ){
                apiErrorCode(40013);
            }
            $appAuthConfig = $APP_AUTH[$appid];
            $myToken = md5( $appid . $request_time . $appAuthConfig['appkey'] );
            
            //apiError( $appid . $request_time . $appAuthConfig['appkey'] );
            //验证时间是否在指定区间内
            if( (NOW_TIME - $request_time)>100 ){        //请求时间5秒之内
                apiErrorCode(40014);
            }
            //验证token
            if( strtoupper($access_token) != strtoupper($myToken) ){
                apiErrorCode(40010);
            }
//        }
        
        
    }
    
    //Api接口访问校验，及数据记载
    private function apiVerification(){
    	
    	
        

    	//接口请求次数增加
    	$api_url = CONTROLLER_NAME .'/'. ACTION_NAME;
    	D('Api')->where( array('api_url'=>$api_url) )->setInc('access_num');
    	
    	/*
    	//其他限制
    	//对用户相关操作需要验证用户user_token，以保证当前对用户的操作或取用户的数据为当前用户本身（用户间相互访问）
    	if( I('uid') && !I('user_token') ){
    		
    	}
    	*/
    	$APP_AUTH 		= C('APP_AUTH');
    	$appid 			= I('appid');
    	$request_time	= I('request_time');
    	$access_token	= I('access_token');
    	if( $appid ){	//开发期间，不传不限制，传了才验证
//    		
        if( !isset($APP_AUTH[$appid]) || empty($APP_AUTH[$appid]) ){
                apiErrorCode(40013);
        }
        $appAuthConfig = $APP_AUTH[$appid];
        $myToken = md5( $appid . $request_time . $appAuthConfig['appkey'] );
//    		
//    		//apiError( $appid . $request_time . $appAuthConfig['appkey'] );
//    		
//    		//验证时间是否在指定区间内
//    		if( (NOW_TIME - $request_time)>10 ){		//请求时间5秒之内
//    			apiErrorCode(40014);
//    		}
//    		/*
//    		echo json_encode(array(
//    				$appid,$request_time,$access_token,$myToken,strtoupper($access_token),strtoupper($myToken)
//    		));exit;
//    		*/
        if( strtoupper($access_token) != strtoupper($myToken) ){
                apiErrorCode(40010);
        }
    	}
    }
    
    
    /*
     * 数据返回格式
    */
    protected function returnJsonData( $res ){
    	return apiNotice(0,$res );
    }
    //数据统一返回操作
    protected function outputJsonData( $res ){
    	echo $this->returnJsonData( $res );
    	exitGetApiData();
    }
    protected function apiDoNotice($ret,$msg='',$expandArr=array() ){
    	apiDoNotice($ret,$msg,$expandArr );
	}
	//参数有效性验证
	protected function check_parameter($requestVal,$errcode){
		
        //数组状态下判断不为空
        if(is_array($requestVal)){
            for ($i=0; $i <count($requestVal) ; $i++) { 
                if( !$requestVal[$i] ){
                    apiErrorCode($errcode);
                }
            }
        }else{
            if( !$requestVal ){
                apiErrorCode($errcode);
            }
        }
		
	}
	//分页信息组装
    protected function getPageInfo3333333333333333(){
    	$page_now 	= I('p')?I('p'):1;
    	$page_total = $this->get('_total');
    	$page_size  = C('LIST_ROWS');
    	$page_count = ceil($page_total/$page_size);
        if(!$page_total){
            $page_total=0;
        }
        
        
    	$pageInfo = array(
    			'page_size' => $page_size,		//每页数量
    			'page_now'	=> $page_now,			//当前页数
    			'page_count'=> $page_count,			//总页数
    			'total' 	=> $page_total			//总数
    	);
    	
    	return $pageInfo;
    }

    //分页信息组装
    protected function getPageInfo(){
        $page_now   = I('p')?I('p'):1;
        $page_total = $this->get('_total');
        $page_size  = C('LIST_ROWS');
        $page_count = ceil($page_total/$page_size);
        
        $pageInfo = array(
                '_page'     =>$this->get('_page'),
                'page_size' => $page_size,          //每页数量
                'page_now'  => $page_now,           //当前页数
                'page_count'=> $page_count,         //总页数
                'total'     => $page_total?$page_total:0,          //总数
        );
        
        return $pageInfo;
    }
    
    
}
