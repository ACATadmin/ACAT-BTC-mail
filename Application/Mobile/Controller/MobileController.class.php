<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Mobile\Controller;
use Common\Controller\CombaseController;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class MobileController extends CombaseController {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		//$this->redirect('Index/index');
	}


    protected function _initialize(){
        
        parent::_initialize();
        
        
        
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }


        $this->uid = session('user_auth.uid');
    
        
    }

//        protected function weiXinLogin(){
//                if( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ){
//                    define('IS_WEIXIN', true);
//                    $configs        = array('appid'=> trim(C('APPID')),'secret'=> trim(C('APPSECRET')));
//                    $this->wechat   = new Wechat($configs);
//                
//                    $access_token = S('ACCESS_TOKEN');
//                    if (!$access_token) {
//                        $access_token = $this->wechat->getToken();
//                        S('ACCESS_TOKEN', $access_token, 7000);
//                    }
//                    $this->wechat->access_token = $access_token;
//                
//                    $jsapiTicket = S('jsapiTicket');
//                    if (!$jsapiTicket) {
//                        $jsapiTicket = $this->wechat->getJsapiTicket();
//                        S('jsapiTicket', $jsapiTicket, 7000);
//                    }
//                
//                    $this->signPackage = $this->wechat->getSignPackage($jsapiTicket);
//                
//                    // $wx_userinfo = $this->wechat->user(session('wx_openid'));
//             
//                }else{
//                    //$url=U("Mobile/Empty/emptys");
//                     //header('Location: '.$url);
//                    define('IS_WEIXIN', false);
//                }
//                
//                $this->assign('IS_WEIXIN',IS_WEIXIN);
//                if( IS_WEIXIN ){  //未登录且在微信环境下打开
//                    
//                    $wx_openid = session('wx_openid');
//                    $this->openid = $wx_openid;
//                    
//                    if( empty( $wx_openid ) ){  //自动登录
//                        $AccessToken = $this->wechat->getOauthAccessToken();
//                        if( $AccessToken ){
//                            $wx_userinfo = $this->wechat->getOauthUserInfo($AccessToken['access_token'],$AccessToken['openid']);
//                            
//                            //sf($wx_userinfo,'wx_openid/wx_user_'.$wx_userinfo['openid'].'_'.time_format(time(),'Y-m-d-H-i-s').'.php');
//                            $wx_openid = $wx_userinfo['openid'];
//                            $this->openid = $wx_openid;
//                            
//                
//                            //记录wx_openid
//                            session('wx_openid',$wx_openid);
//                            session('sex',$wx_userinfo['sex']);
//                            session('headimgurl',$wx_userinfo['headimgurl']);
//                            session('nickname',$wx_userinfo['nickname']);
//                        }else{
//                            //前去认证，获取code
//                            $OAuthRedirectUrl = $this->wechat->getOAuthRedirect(null,'','snsapi_userinfo');
////                            vde($OAuthRedirectUrl);
//                            header("Location:".$OAuthRedirectUrl);
//                        }
//                    }
//                }
//                
//                
//        }
    
    
	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}

        public function loadMore( $view ){
    	$is_load_more = I('load_more');
    	$list_contents = $this->fetch( $view );
    	if( $is_load_more ){
    		echo $list_contents;
    		exit;
    	}
    	$this->assign('list_contents',$list_contents);
    }
}
