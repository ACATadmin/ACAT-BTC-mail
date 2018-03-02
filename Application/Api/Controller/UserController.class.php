<?php
namespace Api\Controller;
use User\Api\UserApi;

/**
 * 会员相关接口
 */
class UserController extends ApiController {
	
	/**
	 * 第三方登录接口（通过openid获取用户信息）
	 * @param unknown $openid
	 */
	public function thirdPartyLogin($wx_openid='',$qq_openid='',$wb_openid='',$wx_openid_cs='',$qq_openid_cs='',$wb_openid_cs=''){
		$map = array();
		if($wx_openid)$map['wx_openid'] = $wx_openid;
		if($qq_openid)$map['qq_openid'] = $qq_openid;
		if($wb_openid)$map['wb_openid'] = $wb_openid;
		if($wx_openid)$map['wx_openid_cs'] = $wx_openid_cs;
		if($qq_openid)$map['qq_openid_cs'] = $qq_openid_cs;
		if($wb_openid)$map['wb_openid_cs'] = $wb_openid_cs;
		
		$uid = D('UcenterMember')->where( $map )->getField('id');
                $status=D('UcenterMember')->where( $map )->getField('status');
		if($uid && $status>0)
		{
			$res=  getApiData('User/getUserInfo',array('uid'=>$uid));
			$this->outputJsonData( $res );
		}
		else 
		{
			//$data['uid'] = '0';
                        $data=null;
			$this->outputJsonData( $data );
		}
	}
	
	private function bindingThirdParty($uid,$wx_openid='',$qq_openid='',$wb_openid='',$wx_openid_cs='',$qq_openid_cs='',$wb_openid_cs=''){
		//绑定第三方帐号
		$thirdPartyData = array();
		if($wx_openid)$thirdPartyData['wx_openid'] = $wx_openid;
		if($qq_openid)$thirdPartyData['qq_openid'] = $qq_openid;
		if($wb_openid)$thirdPartyData['wb_openid'] = $wb_openid;
		if($wx_openid)$thirdPartyData['wx_openid_cs'] = $wx_openid_cs;
		if($qq_openid)$thirdPartyData['qq_openid_cs'] = $qq_openid_cs;
		if($wb_openid)$thirdPartyData['wb_openid_cs'] = $wb_openid_cs;
		if( !empty($thirdPartyData) )D('UcenterMember')->where( array('id'=>$uid) )->save($thirdPartyData);
	}
	

	public function bindThirdParty($mobile,$verify,$password,$repassword,$user_type,$wx_openid='',$qq_openid='',$wb_openid='',$wx_openid_cs='',$qq_openid_cs='',$wb_openid_cs=''){
	
		if(!C('USER_ALLOW_REGISTER')){
			apiError('注册已关闭',-1);
		}
	
		//检测短信验证码
		/*if( $verify!='1234' ){
		 apiErrorCode( 40031 );
		}*/
	
		if( $verify!='1234' ){
			$res = checkMobileCode($mobile,$verify);
			if($res==0){
				apiErrorCode( 40031 );
			}
		}
	
		if(!preg_match('/[0-9|A-Z|a-z]{6,16}/',$password)){
			apiErrorCode( 40029 );
		}
		 
		if($password!=$repassword){
			apiErrorCode( 40001 );
		}
	
		if(I('user_type')==''){
			apiError('用户类型不能为空');
		}
	
		$map['mobile'] = $mobile;
		$member = M('UcenterMember')->where($map)->find();
		
		if($member)
		{
			$this->bindingThirdParty($member['id'],$wx_openid,$qq_openid,$wb_openid,$wx_openid_cs,$qq_openid_cs,$wb_openid_cs);
			$search = array('mobile'=>$mobile,'verify'=>$verify);
			$VerifyCode = D('VerifyCode');
			$VerifyCode->where($search)->delete();
			$res=  getApiData('User/getUserInfo',array('uid'=>$member['id']));
			$this->outputJsonData( $res );
		}
		
		/* 调用注册接口注册用户 */
		$User = new UserApi;
		$uid = $User->register($mobile, $password, $email='',$mobile);
	
		if(0 < $uid){ //注册成功
			//TODO: 发送验证邮件
			$Ring = M('Huanxin');
			$ring_id = $Ring->find();
			//                        $Member->where(array('uid'=>$uid))->save(array('hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password']));
			//                        $Ring->where(array('id'=>$ring_id['id']))->delete();
			//登录，生成member表记录
			D('Member')->login($uid);
			if($user_type==1){
				D('Member')->where('uid='.$uid)->save(array('user_type'=>1,'user_type_backup'=>1,'realname'=>I('realname'),'id_card'=>I('id_card'),'id_card_pics'=>I('id_card_pics'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password']));
			}else if($user_type==2){
				D('Member')->where('uid='.$uid)->save(array('user_type'=>2,'user_type_backup'=>2,'company_simple_name'=>I('company_simple_name'),'company_name'=>I('company_name'),'company_address'=>I('company_address'),'contact'=>I('contact'),'contact_mobile'=>I('contact_mobile'),'business_licence'=>I('business_licence'),'other_message_pic'=>I('other_message_pic'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password']));
				//                            D('CompanyInfo')->add(array('uid'=>$uid,'company_simple_name'=>I('company_simple_name'),'company_name'=>I('company_name'),'company_address'=>I('company_address'),'contact'=>I('contact'),'contact_mobile'=>I('contact_mobile'),'business_licence'=>I('business_licence')));
			}else if($user_type==3){
				D('Member')->where('uid='.$uid)->save(array('user_type'=>3,'user_type_backup'=>3,'nickname'=>I('ngo_name'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password'],'ngo_desc'=>I('ngo_desc'),'head_pic_id'=>I('head_pic_id'),'province_id'=>I('province_id')));
			}else if($user_type==4){
				D('Member')->where('uid='.$uid)->save(array('user_type'=>4,'user_type_backup'=>4,'nickname'=>I('real_name'),'realname'=>I('real_name'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password']));
			}
			$Ring->where(array('id'=>$ring_id['id']))->delete();
			//绑定第三方帐号
			$this->bindingThirdParty($uid,$wx_openid,$qq_openid,$wb_openid,$wx_openid_cs,$qq_openid_cs,$wb_openid_cs);
				
			//			//邀请码，积分变更（邀请码的所有者积分/优惠券增加）
			//			if( $invitation_code ){
			//				D('InvitationCodeRecode')->newUseInvitationCode($uid,$invitation_code);
			//			}
	
			$search = array('mobile'=>$mobile,'verify'=>$verify);
			$VerifyCode = D('VerifyCode');
			$VerifyCode->where($search)->delete();
	
			
			$res=  getApiData('User/getUserInfo',array('uid'=>$member['id']));
			$this->outputJsonData( $res );
		} else { //注册失败，显示错误信息
			apiError($this->showRegError($uid),-1);
		}
	
	
	}
	
        
        
	/**
	 * 登录
	 */
	public function login($mobile,$password,$wx_openid='',$qq_openid='',$wb_openid=''){
		/* 调用UC登录接口登录 */
            
                if(!preg_match('/^0?(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/',$mobile)){
                    apiErrorCode( 40140 );
                }
                
            
                
                $user = new UserApi;
                $uid = $user->login($mobile, $password,3);

                if(!$uid || $uid==-1){
                    $uid = $user->login($mobile, $password);
                }
                
                
		if(0 < $uid){ //UC登录成功
			/* 登录用户 */
			$Member = D('Member');
			if($Member->login($uid)){ //登录用户
				//TODO:跳转到登录前页面
				$res = $Member->where( array('uid'=>$uid) )->find();
                                $res['session_id'] = session_id();
				
				//绑定第三方帐号
				$this->bindingThirdParty($uid,$wx_openid,$qq_openid,$wb_openid,$wx_openid_cs,$qq_openid_cs,$wb_openid_cs);
				$res=  getApiData('User/getUserInfo',array('uid'=>$uid));
                                
                                $this->outputJsonData( $res );
			} else {
				apiError($Member->getError(),40020);
			}
		
		} else { //登录失败
			switch($uid) {
				case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
				case -2: $error = '密码错误！'; break;
				default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
			}
			apiError($error,40020);
		}
	}
	
	/**
	 *注册
	 *手机号、手机验证码、密码
	 */
	public function register($mobile,$verify,$password,$user_type,$profession_type=0){	
		
		if(!C('USER_ALLOW_REGISTER')){
			apiError('注册已关闭',-1);
		}
		
		if( $verify!='1234' ){
                    $res = checkMobileCode($mobile,$verify);
                    if($res==0){
                        apiErrorCode( 40031 );
                    }
                }
                
                if(!preg_match('/^0?(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/',$mobile)){
                    apiErrorCode( 40140 );
                }
                
                if(!preg_match('/[0-9|A-Z|a-z]{6,16}/',$password)){
                    apiErrorCode( 40029 );
                }
               
//                if($password!=$repassword){
//                    apiErrorCode( 40001 );
//                }
                
                $this->check_parameter( $user_type,40138 );
                if($user_type==2){
                    $this->check_parameter( $profession_type,40139 );
                }
                
                
		/* 调用注册接口注册用户 */
		$User = new UserApi;
		$uid = $User->register($mobile, $password, $email='',$mobile);
		
		if(0 < $uid){ //注册成功
			//TODO: 发送验证邮件
                   
                        $m=D('Member');
                        $ring_info=D('Huanxin')->getRing();
                        //登录，生成member表记录
			$m->login($uid);
                        
                        $save_data['user_type'] = $user_type;
                        $save_data['profession_type'] = $profession_type;
                        $save_data['realname'] = $mobile;
                        $save_data['update_time'] = NOW_TIME;
                        if($ring_info){
                            $save_data['hx_username'] = $ring_info['hx_username'];
                            $save_data['hx_password'] = $ring_info['hx_password'];
                        }
                        $m->where('uid='.$uid)->save( $save_data );
                        if($ring_info){
                            D('Huanxin')->where( array('id'=>$ring_info['id']) )->delete();
                        }
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

        
        
	/**
	 *注册
	 *手机号、手机验证码、密码
	 */
	public function is_register($mobile){	
		

			if(!$mobile){
				apiError('手机号码不能为空');
			}
			$member=D('UcenterMember');
			$where['mobile']=$mobile;
			$info=$member->where($where)->find();

			if($info){
				// apiError('手机号码已存在');
				$this->apiDoNotice(1,'手机号码已存在,本次查询');
			}else{
				apiError('手机号码不存在,本次查询失败');
			}
		
		
	}


	/**
	 *注册
	 *手机号、手机验证码、密码
	 */
	public function register_bm($mobile,$verify,$password,$repassword,$user_type,$wx_openid='',$qq_openid='',$wb_openid='',$wx_openid_cs='',$qq_openid_cs='',$wb_openid_cs=''){	
		
		if(!C('USER_ALLOW_REGISTER')){
			apiError('注册已关闭',-1);
		}
		

        if( $verify!='1234' ){
            $res = checkMobileCode($mobile,$verify);
            if($res==0){
                apiErrorCode( 40031 );
            }
        }
        if(!I('nickname')){
            apiError('姓名不能为空！');
        }

        if(!preg_match('/(^\d{11}$)/',$mobile)){
            apiError('手机号码格式不正确');
        }

        if(!preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/',I('id_card'))){
            apiError('身份证格式不正确');
        }
                
        if(!preg_match('/[0-9|A-Z|a-z]{6,16}/',$password)){
            apiErrorCode( 40029 );
        }
               
        if($password!=$repassword){
            apiErrorCode( 40001 );
        }
                
       
                
        

		/* 调用注册接口注册用户 */
		$User = new UserApi;
		$uid = $User->register($mobile, $password, $email='',$mobile);
		
		if(0 < $uid){ //注册成功
			//TODO: 发送验证邮件
		

			//登录，生成member表记录
			D('Member')->login($uid);
         	$data=array(
         		'user_type'=>1,
         		'rg_type'=>1,
         		'nickname'=>I('nickname'),
         		'id_card'=>I('id_card'),
         		);
            D('Member')->where('uid='.$uid)->save($data);
						
			$this->apiDoNotice(1,'注册');
		} else { //注册失败，显示错误信息
			apiError($this->showRegError($uid),-1);
		}
		
		
	}


        /**
         *第三方绑定注册
         *手机号、OPPID、用户基础信息
         */
        public function registerThirdParty($mobile,$verify,$password='',$wx_openid='',$qq_openid='',$wb_openid='',$third_head_url='',$nickname=''){  
            
            if(!C('USER_ALLOW_REGISTER')){
                apiError('注册已关闭',-1);
            }

            if( $verify!='1234' ){
                $res = checkMobileCode($mobile,$verify);
                if($res==0){
                    apiErrorCode( 40031 );
                }
            }

            $mobiles = M('UcenterMember')->where( array('mobile'=>$mobile) )->Field("id,mobile")->find();
             
            if ($mobiles) {
                $map = array();
                if($wx_openid)$map['wx_openid'] = $wx_openid;
                if($qq_openid)$map['qq_openid'] = $qq_openid;
                if($wb_openid)$map['wb_openid'] = $wb_openid;
                //绑定第三方帐号
                $this->bindingThirdParty($mobiles['id'],$wx_openid,$qq_openid,$wb_openid);
                $maps = array();
                if($third_head_url)$maps['third_head_url'] = $third_head_url;
                if($nickname)$maps['nickname'] = $nickname;
                D('Member')->where( array('uid'=>$mobiles['id']) )->save($maps);
                $res = D('Member')->where( array('uid'=>$mobiles['id']) )->find();
                
            }else{
                    $password=substr($mobile, 5);
                     
                    /* 调用注册接口注册用户 */
                    $User = new UserApi;
                    $uid = $User->register($mobile, $password, $email='',$mobile);
                    
                    if(0 < $uid){ //注册成功
                        //TODO: 发送验证邮件
                        
                        //登录，生成member表记录
                        D('Member')->login($uid);
                        
                        //绑定第三方帐号
                        $this->bindingThirdParty($uid,$wx_openid,$qq_openid,$wb_openid);
                        $res = D('Member')->where( array('uid'=>$uid) )->find();

                    } else { //注册失败，显示错误信息
                        apiError($this->showRegError($uid),-1);
                    }

            }
           
          
           $this->outputJsonData( $res ); 
            
            
        }
	
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			//case -1:  $error = '用户名长度必须在16个字符以内！'; break;
                        case -1:  $error = '用户名不能为空！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			//case -3:  $error = '用户名被占用！'; break;
                        case -3:  $error = '手机号被占用！'; break;
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
	
	
	
	
	//=========================================================================================
	/**
	 * 手机号修改密码
	 * @param  $mobile  手机号
	 * @param  $verify  验证码
         * @param  $new_password   新密码
	 */	
        public function resetPasswordByMobile($mobile,$verify,$new_password,$repassword=''){
            $this->check_parameter($mobile,40032);
            $this->check_parameter($verify,40035);
            $this->check_parameter($new_password,40026);
            $this->check_parameter($repassword,40027);
            /*if( $verify!='1234' ){
                apiErrorCode( 40031 );
            }*/
            
            
            if( !preg_match('/[0-9|A-Z|a-z]{6,16}/',$new_password) ){
                apiError('密码格式不正确',1);
            }
            
            if($new_password !== $repassword){
                apiErrorCode( 40028 );
            }
            if( $verify!='1234' ){
                $res = checkMobileCode($mobile,$verify);
                if($res==0){
                    apiErrorCode( 40031 );
                }
            }
            $User = new UserApi;
            $new_password = $User->getPasswordEncrypt($new_password);
            
            $ret=$User->resetPasswordByMobile($mobile, $new_password);
            //成功 清除session
            if($ret){
                // session('verify_phone',null);

                $search = array('mobile'=>$mobile,'verify'=>$verify);
                $VerifyCode = D('VerifyCode');
                $VerifyCode->where($search)->delete();
            }
            $this->apiDoNotice($ret,'修改密码');
        }
        
        /**
	 * 修改密码
	 * @param  $mobile  手机号
	 * @param  $verify  验证码
         * @param  $new_password   新密码
	 */	
        public function changePassword($uid,$old,$password,$repassword){
        	$this->check_parameter($uid,40051);
        	$this->check_parameter($old,40025);
        	$this->check_parameter($password,40026);
        	$this->check_parameter($repassword,40027);
        	if($password !== $repassword){
        		apiErrorCode( 40028 );
            }
            $data['password'] = I('post.password');
            if($old==$password){
                apiError('旧密码与新密码不能相同');
            }
            $Api    =   new UserApi();
            $res    =   $Api->updateInfo($uid, $old, $data);
	        if($res['status']){
	            $this->apiDoNotice(1,'修改密码');
	        }else{
	        	apiError( $res['info'],40020 );
	        }
        }


        /**
         * 修改绑定邮箱
         * @param $newEmail 新邮箱
         */
        public function updateBindingEmail($uid,$newEmail){
            $this->check_parameter($newEmail,40033);

            
            if(!preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/',$newEmail)){
                apiError( '邮箱格式错误',40011 );
            }
            $User = new UserApi;
            $ret=$User->updateBindingEmail($uid,$newEmail);
            $this->apiDoNotice($ret,'修改绑定邮箱');
        }
	
        /**
         * 修改手机验证
         * @param  $uid
         * @param  $oldMobile
         * @param  $password
         */
        public function validateMobileByPassword($uid,$oldMobile,$password){
            $User = new UserApi;
            $password = $User->getPasswordEncrypt($password);
            
            $ret=$User->validateMobileByPassword($uid,$oldMobile,$password);
            $this->apiDoNotice($ret,'验证');
        }
        
        
        /**
         * 修改绑定手机
         * @param $verify   验证码
         * @param $newMobile 新手机
         */
	public function updateBindingMobileo($uid,$newMobile,$verify){
            $this->check_parameter($newMobile,40032);
            /*if( $verify!='1234' ){
                apiErrorCode( 40031 );
            }*/
            if( $verify!='123456' ){
                $res = checkMobileCode($newMobile,$verify);
                if($res==0){
                    apiErrorCode( 40137 );
                }
            }

            //-------------------------xing
                if(D('UcenterMember')->where('mobile='.$newMobile)->find()){
                    $old=D('UcenterMember')->where('id='.$uid)->getField('mobile');
                    if($old!=$newMobile){
                        apiErrorCode( 44060 );
                    }
                }
            //
            
            $User = new UserApi;
            $ret=$User->updateBindingMobile($uid,$newMobile);

            //成功 清除session
            if($ret){
                $search = array('mobile'=>$newMobile,'verify'=>$verify);
                $VerifyCode = D('VerifyCode');
                $VerifyCode->where($search)->delete();

                // session('verify_phone',null);
            }

            $this->apiDoNotice($ret,'修改绑定手机');
        }
        
        /**
         * 修改绑定手机
         * @param $verify   验证码
         * @param $newMobile 新手机
         */
	public function updateBindingMobile($uid,$newMobile,$verify){
            $this->check_parameter($uid,40051);
            $this->check_parameter($newMobile,40032);
            /*if( $verify!='1234' ){
                apiErrorCode( 40031 );
            }*/
            
            if(!preg_match('/^0?(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/',$newMobile)){
                apiError('手机格式不正确',1);
            }
            
            if( $verify!='1234' ){
                $res = checkMobileCode($newMobile,$verify);
                if($res==0){
                    apiErrorCode( 40137 );
                }
            }

            //-------------------------xing
                $map['username|mobile']=$newMobile;
//                $map['id']=array('neq',$uid);
                if(D('UcenterMember')->where($map)->find()){
                    apiErrorCode( 44060 );
                }
            //
            
            $ret=D('UcenterMember')->where( array('id'=>$uid) )->save( array('username'=>$newMobile,'mobile'=>$newMobile) );

            //成功 清除session
            if($ret){
                $search = array('mobile'=>$newMobile,'verify'=>$verify);
                $VerifyCode = D('VerifyCode');
                $VerifyCode->where($search)->delete();

                // session('verify_phone',null);
            }

            $this->apiDoNotice($ret,'修改绑定手机');
        }
	
	/**
         * 更改用户信息
         * @param type $nickname
         * @param type $sex
         * @param type $birthday
         */
        public function updateUserInfo($uid,$nickname,$sex,$birthday){
            $this->check_parameter($nickname,40034);
            $Member = D('Member');
            if($Member->create()){
            	$ret = $Member->save();
            	$this->apiDoNotice($ret,'更改用户信息');
            }else{
            	apiError($Member->getError() ,40012 );
            }
            /*$map=array('nickname'=>$nickname,'sex'=>$sex,'birthday'=>$birthday);
            $where=array('uid'=>$uid);
            $ret=D('Member')->where($where)->save($map);
            $this->apiDoNotice($ret,'更改用户信息');*/
        }
	
	/**
         * 切换用户所在城市
         */
	public function setLocalCityId($uid,$city_id){
            $where=array('uid'=>$uid);
            $map=array('local_city_id'=>$city_id);
            $ret=D('Member')->where($where)->save($map);
            $ret=($ret==1)?'true':'false';
            $this->apiDoNotice($ret,'切换用户所在城市');
        }
	
        
        /**
         * 修改头像
         */
        public function updateHeadPic($uid,$head_pic_id){
            $this->check_parameter($uid,40051);
            $this->check_parameter($head_pic_id,40038);
            $where=array('uid'=>$uid);
            $map=array('head_pic_id'=>$head_pic_id);
            $ret=D('Member')->where($where)->save($map);
            $this->apiDoNotice($ret,'修改头像');
        }
    /**
	 *
	 * @param unknown $type 1 注册 2忘记密码 3设置支付密码 4更改绑定手机5绑定第三方6验证码登录
	 * @param unknown $mobile
	 * @param unknown $minute 验证码存在时间 分钟
	 */
    public function sendVerifyCode($mobile,$type,$minute=10){
    	$this->check_parameter($mobile,40032);
        $this->check_parameter($type,40021);

        //根据type 判断是否注册，以及重复
        $uid = D('UcenterMember')->where(array('mobile'=>$mobile))->getField('id');
        if($type==1||$type==4){
        	if($uid) apiErrorCode( 44060 );
        }elseif($type==2||$type==3){
            if(!$uid) apiErrorCode( 44061 );
        }elseif($type==5){
            // if($uid) apiErrorCode( 44062 );
        }

        $res = sendMobile($mobile,$type,$minute);
        
        //调试期间 返回验证码
        /*if($res<0){
        	$res=0;
        }
        $this->outputJsonData($res,'发送验证码');*/
        
        
        //正式期间
        if($res==0){
        	$res=1;
        }else{
        	$res=0;
        }
        $this->apiDoNotice($res,'发送验证码');

    }

        /**
         *
         * @param unknown $type 1 注册 2忘记密码 3设置支付密码 4更改绑定手机
         * @param unknown $mobile
         * @param unknown $minute 验证码存在时间 分钟
         */
        public function sendVerifyCodes($mobile,$type,$minute=10){
            $this->check_parameter($mobile,40032);
            $this->check_parameter($type,40021);

       
            $res = sendMobile($mobile,$type,$minute);
            //调试期间 返回验证码
            /*if($res<0){
                $res=0;
            }
            $this->outputJsonData($res,'发送验证码');*/
            
            //正式期间
            if($res==0){
                $res=1;
            }else{
                $res=0;
            }
            $this->apiDoNotice($res,'发送验证码');

        }


    public function getUserInfo($uid,$mid=''){
    	$this->check_parameter($uid,40051);
        
        $status=D('Member')->where(array('uid'=>$uid))->getField('status');
        if($status!=1){
            $this->outputJsonData(array());
            exit;
        }
        
    	$map = array('uid'=>$uid);
    	$prefix   = C('DB_PREFIX');
        $m_table  = $prefix.('member');
        $um_table  = $prefix.('ucenter_member');
        $field = 'um.mobile,m.*';
        $userInfo     = M() ->table( $m_table.' m' )
                       ->field($field) 
                       ->where( $map )
                       ->join ( $um_table.' um ON m.uid=um.id' )
                       ->find();
    	$userInfo=D('Member')->format($userInfo);

    	
        
        //获取我的钱包余额
        $userInfo['user_money']=D('AccountLog')->getUserMoney($uid);
        //
        
        //获取总收益
        $userInfo['earn_money']=D('AccountLog')->getEarnMoney($uid);
        
        //可提现金额
        $userInfo['can_withdraw_money']=$userInfo['user_money'];
        
        //我支出的赞赏金额
        $userInfo['my_tip_pay'] = D('TipOrder')->getUserTipAmount($uid);
        
        //我支出的总额
        $userInfo['my_pay'] = D('Member')->getOutAmount($uid);
        //
        
       //用户积分
        //$userInfo['pay_points']=D('AccountLog')->getPayPoints($uid);
        //
        
       
        
        //判断是否已设置过支付密码
        $userInfo['is_set_payment']=$userInfo['payment_code']?1:0;
        //
        
//        //判断是否有咨询草稿
//        $draft_article_id=D('Article')->where( array('uid'=>$uid,'article_attr'=>2,'status'=>1) )->getField('id');
//        $userInfo['draft_article_id']=$draft_article_id?$draft_article_id:0;
//        //
        
        
        $message = D('Message');
        $userInfo['unread_message_check']=$message->getUserUnReadNum($uid,2);
        $userInfo['unread_message_system']=$message->getUserUnReadNum($uid,1);
        $userInfo['lastest_check_message'] = $message->getLastestMessage($uid,2);
        $userInfo['lastest_system_message'] = $message->getLastestMessage($uid,1);
        
        

        
        
//        $share['share_title'] = '邀请码';
//        $share['share_description'] = '您的好友邀请您加入原创平台';
//        $card=get_cover($userInfo['head_pic_id']);
//        $share['share_image'] = 'http://' . $_SERVER['HTTP_HOST'] .$card['path'];
//        $share['share_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/Mobile/Share/register/code/'.$userInfo['invite_code'];
//        $userInfo['share'] = $share;
        
        
        $focus = D('Focus');
        $userInfo['focus_num'] = $focus->getUserFocusNum($uid);
        if($mid){
            $userInfo['mid_is_focus'] = $focus->checkHasFocus($mid,1,$uid);
        }
        
        
        $ask = D('Ask');
        $userInfo['free_ask_num'] = $ask->getUserBeAskedNum($uid,2);
        $userInfo['ask_num'] = $ask->getUserAskNum($uid);
        $userInfo['be_asked_num'] = $ask->getUserBeAskedNum($uid);
        
        
        $userInfo['collect_num'] = D('FocusCollect')->getUserCollectZanNum($uid,1);
        
        $userInfo['video_num'] = D('Video')->getUserVideoNum($uid);
        
        $userInfo['ask_price_notice'] = D('Config')->getAskPriceNotice($uid);
        
        $live = D('Live');
        $userInfo['living_id'] = $live->getUserLivingId($uid);
        $userInfo['live_num'] = $live->getUserLiveNum($uid);
        
        $m = D('Member');
        $userInfo['qrcode'] = $m->getUserQrcode($uid);
        $userInfo['share_info'] = $m->getShareInfo($uid);
        
        
        //二维码分享
        $user_qr_code = getApiData('User/createuserqrcode',array('uid'=>$uid));
        $qr_share_info['share_url'] = $user_qr_code['url'];
        $qr_share_info['share_title'] = $userInfo['realname'].'的二维码';
        $qr_share_info['share_content'] = '扫一扫上面的二维码图片，关注专家信息';
        $qr_share_info['share_image_url'] = $userInfo['qrcode'];
        //
        $userInfo['qr_share_info'] = $qr_share_info;
        
        $this->outputJsonData( $userInfo );
    }


    
        
    /**
     * 发送短信验证码接口
     */
        public function sendSmsCode($mobile){
            $sms_tpl = C('SMS_TPL');
            $this->sendSms($mobile, $sms_tpl['verification_code']);
        }
        
    /**
     * 发送普通短信接口
     */
        public function sendSmsText($mobile , $content){
            $this->sendSms($mobile, $content);
        }

        /**
         * 根据城市名获取城市id
         * @return 
         */
        public  function getCityId($city_name){
            $this->check_parameter($city_name,400048);
            $map['region_name'] = array('like', '%'.$city_name.'%');
            $data=M("Region")->where($map)->find();
       
            $this->outputJsonData( $data['region_id'] );
            
        }

        /**
         *
         * @param unknown $type 1 微信 2qq 3微博
         * @param unknown $oppid
         * @param unknown $minute 验证码存在时间 分钟
         */
        public function checkBinding($openid,$type){
            $this->check_parameter($openid,"openid不能为空");
            $this->check_parameter($type,"type不能为空");
            $map = array();
            if ($type==1) {
                $map['wx_openid'] = $openid;
            }elseif($type==2){
                $map['qq_openid'] = $openid;
            }elseif($type==3){
                $map['wb_openid'] = $openid;
            }
         

            $id = M('UcenterMember')->where( $map )->getField("id");
             
            if ($id) {
                $res = D('Member')->where( array('uid'=>$id) )->find();
            }else{
                $res=0;
            }
   
            $this->outputJsonData( $res );

        }

        public function bindingThirdPartys($uid,$wx_openid='',$qq_openid='',$wb_openid=''){
            //绑定第三方帐号
         
            $thirdPartyData = array();
            if($wx_openid)$thirdPartyData['wx_openid'] = $wx_openid;
            if($qq_openid)$thirdPartyData['qq_openid'] = $qq_openid;
            if($wb_openid)$thirdPartyData['wb_openid'] = $wb_openid;
          
            $res=D('UcenterMember')->where( array('id'=>$uid) )->save($thirdPartyData);

            if ($res) {
                $res = 1;
            }else{
                $res=0;
            }
            $this->outputJsonData( $res );
        }
        
    



    /**
	 *注册
	 *手机号、手机验证码、密码
	 */
	public function register_new($mobile="",$password="",$repassword="",$member_type=''){	
		
		if(!C('USER_ALLOW_REGISTER')){
			apiError('注册已关闭',-1);
		}
        $this->check_parameter($mobile,"手机号不能为空");
        $this->check_parameter($password,"手机号不能为空");
        $this->check_parameter($repassword,40039);

    
        $this->check_parameter($member_type,40052);
        if($member_type==1||$member_type==5){
            $this->check_parameter(I('company_name'),40036);
        }

       
        $_POST['user_type'] = 6;

		// $this->check_parameter(I('member_contacts'),40037);

        // $username = $this->create_username();
		/* 调用注册接口注册用户 */
		$User = new UserApi;
		$uid = $User->register($mobile, $password, $email='',$mobile);
		
		if(0 < $uid){ //注册成功
			//TODO: 发送验证邮件
			
			//登录，生成member表记录
			D('Member')->login($uid);
          
			//绑定第三方帐号
			

            //发送短信
            // $member_type=get_member_type($uid);
            // $mobile=getMobile($uid);
            // if ($member_type==5) {
            //    $content=C('SMS_MODEL');
            //    $content=str_replace( "#user#", $mobile , $content[3]['name']);
            //    sendSms($mobile,$content);
            // }else{
            //    $content=C('SMS_MODEL');
            //    $content=str_replace( "#user#", $mobile , $content[2]['name']);
            //    sendSms($mobile,$content);
            // }
            
  
			$this->apiDoNotice(1,'注册');
		} else { //注册失败，显示错误信息
			apiError($this->showRegError($uid),-1);
		}
		
		
	}
        
        public function sendCoupon($uid){
            $couponType=D('CouponType')->where('id=3')->find();
            if($couponType){
                $data['coupon_type_id']=$couponType['id'];
                $data['key']='';
                $data['coupon_type_name']=$couponType['coupon_type_name'];
                $data['coupon_price']=$couponType['price'];
                $data['uid']=$uid;
                $data['min_consumption_amount']=$couponType['min_consumption_amount'];
                $codes=D('Coupon')->createCouponCode(1);
                $data['code']=$codes[0];
                $data['get_time'] = time();
                $ret=D('Coupon')->add($data);
                if($ret){
                    $OverdueTime = D('Coupon')->getNewCodeOverdueTimeById($ret);
                    $data = array('overdue_time'=>$OverdueTime);

                    D('Coupon')->where('id='.$ret)->save($data);

                    // 存在指定用户，添加系统消息

    //	        $data = array(
    //	        			'to_uid'=>$uid,
    //	        			'message_type'=>3,
    //	        			'type'=>1,
    //	        			'create_time'=>time(),
    //	        			'title'=>'您获得一个优惠券',
    //	        			'content'=>'您有一个系统优惠券，请注意查收',
    //                                        'target_type'=>2,
    //                                        'target_id'  =>$ret,
    //	        			);
                    $data = array(
                                            'to_uid'=>$uid,
                                            'message_type'=>3,
                                            'type'=>1,
                                            'create_time'=>time(),
                                            'title'=>'You have a new coupon',
                                            'content'=>'You have a new coupon , please remember to check.',
                                            'target_type'=>2,
                                            'target_id'  =>$ret,
                                            );
                    D('Message')->add($data);
                }
            }
            
        }
        
        public function realNameAuthentication($uid,$profession_type,$realname,$id_card_front=0,$id_card_back=0,$doctor_nurse_certificate=0,$doctor_nurse_qualification_certificate=0,$d_t_p_qualification_certificate=0,$personal_specialty_pic=0,$personal_specialty_video=0,$product_certificate=0,$product_pic=0,$business_licence=0,$qualification_certificate=0,$institutional_certificate=0){
            
            $this->check_parameter($uid,40051);
            $this->check_parameter($profession_type,40139);
            $this->check_parameter($realname,44036);
            
            $m=D('Member');
            $mri=D('MemberRealnameInfo');
            
            $uinfo=$m->find($uid);
            
            
            //判断用户状态
            if(!$uinfo || $uinfo['status']!=1){
                apiError('用户不存在或已禁用',1);
            }
            //
            //判断是否可认证
            if($uinfo['user_type']!=1 && $uinfo['user_type']!=2){
                apiError('您所处身份不可认证',1);
            }
            //
            //判断是否已上传
            if($uinfo['realname_status']==1){
                apiError('您已上传资料，请等待后台审核',1);
            }
            //
            //判断是否已通过
            if($uinfo['realname_status']==2){
                apiError('您已认证通过，无需再次上传',1);
            }
            //
           
            
            //上传资料不能为空
            $this->check_parameter($id_card_front, 40141);
            $this->check_parameter($id_card_back, 40142);
            switch($profession_type){
                case 1:
                    $this->check_parameter($doctor_nurse_certificate, 40143);
                    $this->check_parameter($doctor_nurse_qualification_certificate, 40144);
                    break;
                case 2:
                    $this->check_parameter($d_t_p_qualification_certificate, 40145);
                    break;
                case 3:
                    $this->check_parameter($personal_specialty_pic, 40146);
//                    $this->check_parameter($personal_specialty_video, 40147);
                    break;
                case 4:
                    $this->check_parameter($product_certificate, 40148);
                    $this->check_parameter($product_pic, 40149);
                    break;
                case 5:
                    $this->check_parameter($business_licence, 40150);
                    $this->check_parameter($qualification_certificate, 40151);
                    $this->check_parameter($institutional_certificate, 40152);
                    break;
            }
            //
            
            $TransModel = M();          
            $TransModel->startTrans();
            
            $mri_data=$mri->create();
            if($mri_data){
                //判断是否有记录
                $has=$mri->where( array('uid'=>$uid) )->find();
                if($has){
                    
                    
                    $close_check = C('CLOSE_CHECK');
                    if($close_check!=1){
                        $mri_data['check_status']=0;
                    }
                    
                    
                    
                    
                    $ret=$mri->where( array('uid'=>$uid) )->save($mri_data);
                }else{
                    
                    $close_check = C('CLOSE_CHECK');
                    if($close_check==1){
                        $mri_data['check_status']=1;
                    }
                    
                    $ret=$mri->add($mri_data);
                }
                if(!$ret){
                    $TransModel->rollback();
                    apiError('上传失败',1);
                    exit;
                }
                //
                
                
                //记录到Member表
                if($uinfo['user_type']==1){
                    $m_data['user_type']=2;
                    $m_data['normal_to_profession']=1;
                }
                $m_data['profession_type']=$profession_type;
                
                
                $close_check = C('CLOSE_CHECK');
                if($close_check==1){
                    $m_data['realname_status']=2;
                }else{
                    $m_data['realname_status']=1;
                }
                
                
                $m_data['realname'] = $realname;
                $m_data['update_time']=NOW_TIME;
                $ret=$m->where( array('uid'=>$uid) )->save($m_data);
                
                if(!$ret){
                    $TransModel->rollback();
                    apiError('上传失败',1);
                    exit;
                }
                //
                
                $TransModel->commit();
                
                //设置默认问询信息
                if($ret && $m_data['realname_status']==2){
                    $default_ask_time = D('MemberAskTime')->getDefaultTimeArr();
                    $ask_time_ret = D('MemberAskTime')->addAskTimeMore($uid,$default_ask_time);
                    $ask_price_ret = D('MemberAskPrice')->updateAskPrice($uid,2,0);
                }
                //
                
                $this->apiDoNotice($ret,'上传');
                
            }else{
                $TransModel->rollback();
                apiError('上传失败',1);
                exit;
            }
            
        }
        
        //升职
        public function upToProfession($uid,$profession_type){
            $this->check_parameter($uid,40051);
            $this->check_parameter($profession_type,40139);
            
            //判断是否可以升职
            $check = D('Member')->checkCanUpToProfession($uid);
            if($check['error']){
                apiError($check['msg'],$check['error']);
            }
            //
            
            $save_data['user_type'] = 2;
            $save_data['profession_type'] = $profession_type;
            $save_data['normal_to_profession'] = 1;
            $save_data['uid'] = $uid;
            
            $ret = D('Member')->save($save_data);
            
            $this->apiDoNotice($ret,'操作');
        }
        
        //获取环信用户头像
        public function getHuanxinUserHead(){
            $hx_username = I('hx_username');
            $head_pic_id =D('Member')->where( array('hx_username'=>$hx_username) )->getField('head_pic_id');
            if($head_pic_id){
                $head_pic_url = get_cover($head_pic_id);
                $head_pic_url = $_SERVER["DOCUMENT_ROOT"].$head_pic_url['path'];
            }else{
                $head_pic_url = $_SERVER["DOCUMENT_ROOT"].'/favicon.ico';
            }
            
            
            //用以解决中文不能显示出来的问题 
            $file_name=iconv("utf-8","gb2312",'down'); 
            //$file_sub_path=$_SERVER['DOCUMENT_ROOT']."marcofly/phpstudy/down/down/"; 
            $file_path=$head_pic_url; 
            //首先要判断给定的文件存在与否 
            if(!file_exists($file_path)){ 
            echo "没有该文件文件"; 
            return ; 
            } 
            $fp=fopen($file_path,"r"); 
            $file_size=filesize($file_path); 
            //下载文件需要用到的头 
            Header("Content-type: application/octet-stream"); 
            Header("Accept-Ranges: bytes"); 
            Header("Accept-Length:".$file_size); 
            Header("Content-Disposition: attachment; filename=".$file_name); 
            $buffer=1024; 
            $file_count=0; 
            //向浏览器返回数据 
            while(!feof($fp) && $file_count<$file_size){ 
            $file_con=fread($fp,$buffer); 
            $file_count+=$buffer; 
            echo $file_con; 
            } 
            fclose($fp);
            exit;
        }
        
        
        
        public function createuserqrcode()
        {
            $uid = I('uid');
            $listField = 'm.*,um.email,um.mobile,um.username,um.id,p.path';
            $where=array('m.uid'=>$uid);

            $info = M()->table('ysyy_member m')
            ->field($listField )
            ->join('ysyy_ucenter_member um ON m.uid=um.id','left')
            ->join('ysyy_picture p ON m.head_pic_id=p.id','left')
            ->where( $where )
            ->find();

            $qrcode = M('qrcode')->where(array('uid'=>$uid))->find();
            $path = './Uploads/qrcode/';
            $water = $path.$qrcode['uid'].'_'.$qrcode['level'].'_'.$qrcode['size'].'_'.$qrcode['update_time'].'.png';


            $info = D('Member')->format($info);
            $white = './qrcode/tools/white.png';
            $final = './qrcode/'.$uid.'.jpg';
            $output = 'http://' . $_SERVER['HTTP_HOST'].'/qrcode/'.$uid.'.jpg';
            $head = '.'.$info['path'];
            if(!$info['path'])
            {
                    $head = './Public/Mobile/images/logo2.png';
            }
            if(!file_exists($head))
            {
                    apiError('获取失败！');
            }
            $head128 = './qrcode/tmp/'.$uid.'.jpg';
            $hei = './qrcode/tools/hei.ttf';
            //$final = './qrcode/final.jpg';
                    $image = new \Think\Image();
                    
                    $image->open($head)
                                    ->thumb(128, 128,\Think\Image::IMAGE_THUMB_FIXED)->save($head128);
                    
                    $image->open($white)
                                    ->water($head128,array(46,46),100)
                                    ->water($water,array(88,246),100)
                                    ->text($info['nickname'],$hei,26,'#000000',array(190,66))
                                    ->text($info['province_exp'].' '.$info['city_exp'],$hei,18,'#909090',array(190,116))
                                    ->save($final);
                    
            if(file_exists($final))
            {
                    $data['url'] = $output;
                    $this->outputJsonData( $data);
            }
            else 
            {
                    apiError('生成失败！');
            }

        }
        
}