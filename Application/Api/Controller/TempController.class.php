<?php
namespace Api\Controller;
use User\Api\UserApi;

/**
 * 会员相关接口
 */
class TempController extends ApiController {
	
	/**
	 *注册
	 *手机号、手机验证码、密码
	 */
	public function register(){	
		
	    $mobile = I('mobile');
	    $verify = I('verify');
	    $password = 'yiyuan';
	    $user_type = 1;
		if(!C('USER_ALLOW_REGISTER')){
			apiError('注册已关闭',-1);
		}
		
        if(!preg_match('/[0-9|A-Z|a-z]{6,16}/',$password)){
            apiErrorCode( 40029 );
        }
		/* 调用注册接口注册用户 */
		$User = new UserApi;
		$uid = $User->register($mobile, $password, $email='',$mobile);
		
		if(0 < $uid){ //注册成功
			//TODO: 发送验证邮件
		    $Ring = M('Huanxin');
		    $m=D('Member');
		    $ring_id=$this->getRing();
		    
		    //登录，生成member表记录
		    $m->login($uid);
		    if($user_type==1){
		        $m->where('uid='.$uid)->save(array('user_type'=>1,'user_type_backup'=>1,'rg_type'=>101,'realname'=>I('realname'),'id_card'=>I('id_card'),'id_card_pics'=>I('id_card_pics'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password']));
		    }else if($user_type==2){
		        $m->where('uid='.$uid)->save(array('user_type'=>2,'user_type_backup'=>2,'company_simple_name'=>I('company_simple_name'),'company_name'=>I('company_name'),'company_address'=>I('company_address'),'contact'=>I('contact'),'contact_mobile'=>I('contact_mobile'),'business_licence'=>I('business_licence'),'other_message_pic'=>I('other_message_pic'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password']));
		        //                            D('CompanyInfo')->add(array('uid'=>$uid,'company_simple_name'=>I('company_simple_name'),'company_name'=>I('company_name'),'company_address'=>I('company_address'),'contact'=>I('contact'),'contact_mobile'=>I('contact_mobile'),'business_licence'=>I('business_licence')));
		    }else if($user_type==3){
		        $m->where('uid='.$uid)->save(array('user_type'=>3,'user_type_backup'=>3,'nickname'=>I('ngo_name'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password'],'ngo_desc'=>I('ngo_desc'),'head_pic_id'=>I('head_pic_id'),'province_id'=>I('province_id'),'country_id'=>I('country_id'),'ngo_type'=>I('ngo_type')));
		        D('UcenterMember')->where('id='.$uid)->save(array('mobile'=>''));
		    }else if($user_type==4){
		        $m->where('uid='.$uid)->save(array('user_type'=>4,'user_type_backup'=>4,'nickname'=>I('real_name'),'realname'=>I('real_name'),'hx_username'=>$ring_id['hx_username'],'hx_password'=>$ring_id['hx_password']));
		    }
		    $Ring->where(array('id'=>$ring_id['id']))->delete();
		    $asm=D('AccordSendMessage');
		    if($user_type==1 || $user_type==2){
		        if($asm->where('id=1')->getField('status')==1){$m->sendRegisterMobile($mobile);}//发送注册成功短信
		        $m->createRegisterMessage($uid);//发送注册成功系统消息
		    }
		    
		    $search = array('mobile'=>$mobile,'verify'=>$verify);
		    $VerifyCode = D('VerifyCode');
		    $VerifyCode->where($search)->delete();
			$this->apiDoNotice(1,'注册');
		} else { //注册失败，显示错误信息
			apiError($this->showRegError($uid),-1);
		}
		
		
	}
	public function getRing(){
	    $Ring = M('Huanxin');
	    $ring_id = $Ring->find();
	    if(D('Member')->where('hx_username='.$ring_id['hx_username'])->find()){
	        $Ring->where(array('id'=>$ring_id['id']))->delete();
	        $ring_id=$this->getRing();
	    }
	    return $ring_id;
	}
    /**
	 *
	 * @param unknown $type 1 注册 2忘记密码 3设置支付密码 4更改绑定手机5绑定第三方
	 * @param unknown $mobile
	 * @param unknown $minute 验证码存在时间 分钟
	 */
    public function sendVerifyCode(){
        $mobile = I('mobile');
        $type = 1;
        $minute = 10;
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
}