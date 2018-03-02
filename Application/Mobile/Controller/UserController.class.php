<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Mobile\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends MobileController {

	/* 用户中心首页 */
	public function index(){
		
	}

	/* 注册页面 */
	public function registerold($username = '', $password = '', $repassword = '', $email = '', $verify = ''){
        if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
		if(IS_POST){ //注册用户
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}

			/* 检测密码 */
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}			

			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email);
			if(0 < $uid){ //注册成功
				//TODO: 发送验证邮件
				$this->success('注册成功！',U('login'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}

		} else { //显示注册表单
			$this->display();
		}
	}
        
 //        public function register($mobile='',$verify='',$password='',$invitation_code='',$wx_openid=''){
 //            if(IS_POST){
 //            $opts['mobile']=I('mobile');
 //            $opts['password']=I('password');
 //            $opts['verify']=I('verify');
 //            $data = getApiData('User/register',$opts);
 //            $this->ajaxReturn($data);
 //            }else{
 //            //vde($data);
 //                $this->display();
 //            }
	// }

		/**
		 *注册
		 *手机号、手机验证码、密码
		 */
		public function registers($mobile,$verify,$password,$wx_openid='',$qq_openid='',$wb_openid=''){	
			
			if(!C('USER_ALLOW_REGISTER')){
				apiError('注册已关闭',-1);
			}
			
			// vde($mobile);

	        if( $verify!='1234' ){
	            $res = checkMobileCode($mobile,$verify);
	            if($res==0){
	                apiErrorCode( 40031 );
	            }
	        }
			
			/* 调用注册接口注册用户 */
			$User = new UserApi;
			$uid = $User->register($mobile, $password, $email='',$mobile);
			
			if(0 < $uid){ //注册成功
				//TODO: 发送验证邮件
				
				//登录，生成member表记录
				D('Member')->login($uid);
				
				//绑定第三方帐号
				$this->bindingThirdParty($uid,$wx_openid,$qq_openid,$wb_openid);
				


	            $search = array('mobile'=>$mobile,'verify'=>$verify);
	            $VerifyCode = D('VerifyCode');
	            $VerifyCode->where($search)->delete();
							
				$this->apiDoNotice(1,'注册');
			} else { //注册失败，显示错误信息
				apiError($this->showRegError($uid),-1);
			}
			
			
		}

		/* 注册页面 */
		public function register($mobile='',$verify='',$password='',$invitation_code='',$wx_openid=''){
	        if(!C('USER_ALLOW_REGISTER')){
	            $this->error('注册已关闭');
	        }
			if(IS_POST){ //注册用户
				/* 检测验证码 */
				if( $verify!='123456' ){
	                $res = checkMobileCode($mobile,$verify);
	                if($res==0){
	                	$this->error('验证码错误！');
	                }
	            }
	      

				$User = new UserApi;
				// $uid = $User->register($mobile, $password, $email='',$mobile,$wx_openid);
				$uid = $User->register($mobile, $password, $email='',$mobile);
				
				if(0 < $uid){ //注册成功
					//TODO: 发送验证邮件
					
					//登录，生成member表记录
					$ret= D('Member')->login($uid);
					if($ret){
					
						$this->success('注册成功',U('Index/index'));	

					}else{
						
						$this->error('注册失败');
					}		
				} else { //注册失败，显示错误信息
					$this->error($this->showRegError($uid));
				}

			} else { //显示注册表单

				if ( is_login() ) {
					$this->redirect( 'Index/index' );
				}
				$this->back_url = session('back_url');
				$this->title='注册';
				$this->display();
			}
		}

	/* 登录页面 */
	public function loginold($username = '', $password = '', $verify = ''){
		if(IS_POST){ //登录验证
			/* 检测验证码 */
//			if(!check_verify($verify)){
//				$this->error('验证码输入错误！');
//			}

			/* 调用UC登录接口登录 */
			$user = new UserApi;
			$uid = $user->login($username, $password);
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$Member = D('Member');
				if($Member->login($uid)){ //登录用户
					//TODO:跳转到登录前页面
                                        
					$this->success('登录成功！',U('Mobile/Index/index'));
				} else {
					$this->error($Member->getError());
				}

			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}

		} else { //显示登录表单
			$this->display();
		}
	}
        
        public function logins($username = '', $password = '', $verify = ''){
		if(IS_POST){ //登录验证
			$data = getApiData('User/login',array('mobile'=>$username,'password'=>$password));
                        D('Member')->login($data['uid']);
                        $this->ajaxReturn($data);
                } else { //显示登录表单
			$this->display();
		}
	}
        
        

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			//$this->success('退出成功！', U('User/login'));
                        $this->redirect('User/login');
		} else {
			$this->redirect('User/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}


    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('User/login') );
		}
        if ( IS_POST ) {
            //获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if($res['status']){
                $this->success('修改密码成功！');
            }else{
                $this->error($res['info']);
            }
        }else{
            $this->display();
        }
    }
    
    public function forget_password(){
        $this->display();
    }

    /* 登录页面 */
    public function login($username = '', $password = '', $verify = ''){
    	if(IS_POST){ //登录验证
    		$user = new UserApi;
    		$uid = $user->login($username, $password);
    		// vde($uid);
    		if(0 < $uid){ //UC登录成功
    			/* 登录用户 */
    			$Member = D('Member');
    			if($Member->login($uid)){ //登录用户
    				//TODO:跳转到登录前页面
    				$Member->where( array('uid'=>$uid) )->find();
    				$this->success('登陆成功',U('Index/index'));
    			} else {
    				$this->error($Member->getError());
    			}
    		
    		} else { //登录失败
    			switch($uid) {
    				case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
    				case -2: $error = '密码错误！'; break;
    				default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
    			}
    			$this->error($error);
    		}
    		/*$data= getApiData('User/login',$_POST );
    		if($data['errcode']){
    			echo json_encode($data);
    		}else{
    			D('Member')->login($data['uid']);
    			$this->success('登陆成功',U('Account/index'));
    		}*/
    		

    	} else { //显示登录表单
    		// if ( is_login() ) {
    		// 	$this->redirect( 'Account/index' );
    		// }
    		// $this->back_url = session('back_url');
    		// $this->title='登陆';
    		$this->display();
    	}
    }

}
