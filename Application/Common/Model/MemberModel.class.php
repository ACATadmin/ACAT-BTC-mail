<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Common\Model;
use User\Api\UserApi;

/**
 * 文档基础模型
 */
class MemberModel extends BaseModel{

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('login', 0, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('last_login_ip', 0, self::MODEL_INSERT),
        array('last_login_time', 0, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', 1, self::MODEL_INSERT),
    );

    /**
     * 格式化
     */
    public function format( $info ){
        if(!$info){
            return $info;
        }
        
        
        $info['user_type_exp'] = getCName('USER_TYPE',$info['user_type']);
        $info['company_id_exp'] = D('Company')->where( array('id'=>$info['company_id']) )->getField('company_name');
        
        if($info['password']){
            //密码变成*
            $password_length = strlen($info['password']);
            $password_exp = '';
            for($i=1;$i<=$password_length;$i++){
                $password_exp.='*';
            }
            $info['password_exp'] = $password_exp;
            //
            $info['password_exp'] = '******';
        }
        
        //当前用户企业列表
        $info['company_select_list'] = D('Company')->getUserSelectCompanyList($info['uid']);
        //
        
        
        return $info;
    }
    
    
    
    
    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function logins($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
        if(!$user){ //未注册
            /* 在当前应用中注册用户 */
        	$Api = new UserApi();
        	$info = $Api->info($uid);
            $user = $this->create(array('nickname' => $info[1], 'status' => 1));
            $user['uid'] 				= $uid;
            //$user['invitation_code'] 	= D('InvitationCodeRecode')->createInvitationCode();	//创建用户邀请码
            
            //用户登录后将当前登录用户的之前操作的记录中未标识其uid的重置为当前用户的uid
            $this->relevanceSessionToUid( $uid );
            
            
            if(!$this->add($user)){
                $this->error = '前台用户信息注册失败，请重试！';
                return false;
            }
        } elseif(1 != $user['status']) {
            $this->error = '用户未激活或已禁用！'; //应用级别禁用
            return false;
        }

        /* 登录用户 */
        $this->autoLogin($user);

        //记录行为
        action_log('user_login', 'member', $uid, $uid);

        return true;
    }
    
    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
		
        
        if(!$user){ //未注册
        	/* 在当前应用中注册用户 */
        	$Api = new UserApi();
        	$info = $Api->info($uid);
        	$user = $this->create(array('nickname' => $info[1], 'status' => 1));
        	$user['uid'] 				= $uid;
        	//$user['invitation_code'] 	= D('InvitationCodeRecode')->createInvitationCode();	//创建用户邀请码
        
        	//用户登录后将当前登录用户的之前操作的记录中未标识其uid的重置为当前用户的uid
        	$this->relevanceSessionToUid( $uid );
        
        
        	if(!$this->add($user)){
        		$this->error = '前台用户信息注册失败，请重试！';
        		return false;
        	}
        } elseif(1 != $user['status']) {
        	$this->error = '用户未激活或已禁用！'; //应用级别禁用
        	return false;
        }
        /*
        if(!$user || 1 != $user['status']) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }
		*/
        
        //记录行为
        action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }
    //将当前登录用户的之前操作的记录中未标识其uid的重置为当前用户的uid
    public function relevanceSessionToUid( $uid ){
    	
    	$map = array(
    			'session_id'	=> session_id(),
    			'uid'			=> 0
    	);
    	
    	//1、设置搜索记录表
    	D('SearchRecord')->where($map)->save( array('uid'=>$uid) );
    	
    	//2、浏览轨迹表
    	
    }
    
	
    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }
	
    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user['uid'],
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => get_username($user['uid']),
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }
    
    
    
    
    //==========================================================================================
    /**
     * 获取优惠码所属用户uid
     * @param unknown $invitation_code
     */
    public function getInvitationCodeUid( $invitation_code ){
    	return $this->where( array('invitation_code'=>$invitation_code) )->getField('uid');
    }
    
    /**
         * 验证支付密码
         * @param $uid   用户id
         * @param $payment_code 支付密码
         */
    public function verifyPayPassword($uid,$payment_code){
        $User = new UserApi;
        $payment_code = $User->getPasswordEncrypt($payment_code);
        $yespayment_code = $this->where('uid='.$uid)->getField('payment_code');
        if($payment_code==$yespayment_code){
            return true;
        }else{
            return false;
        }
    }
    

     //获取单个用户信息
    public function getUserInfo( $uid ){
        if( is_array($uid) ){
            $map = $uid;
        }else{
            $map = array('m.uid'=>$uid);
        }
         
        $info = $this   ->table('vb_member m')
                        ->join('vb_ucenter_member mi ON m.uid=mi.id','left')
                        ->where( $map )->find();
        // $info = $this->format( $info );
         
        return $info;
    }

     //获取单个用户信息
    public function getUserInfos( $uid ){
        $map['uid'] = $uid;
        $info = $this->where( $map )->find();
        $infos['img_exp']=get_cover_path($info['head_pic_id']);
        $infos['uid']=$info['uid'];
         
        return $infos;
    }
    
    public function sendRegisterMobile($mobile){
        $content = "恭喜您，注册成功！";
        $sendmobile=$mobile;
        $ret = sendSms($sendmobile,$content,array('template'=>'2'));
    }
    
    public function createRegisterMessage($uid){
//        $data=array(
//            'content'=>'恭喜您，成功注册为会员！',
//            'title'  =>'恭喜',
//            'type'   =>2,
//            'message_type' =>1,
//            'create_time' =>time(),
//            'to_uid'      =>$uid,
//        );
        $data=array(
            'content'=>'Congratulations , you have been successfully registered as a member！',
            'title'  =>'Congratulations',
            'type'   =>2,
            'message_type' =>1,
            'create_time' =>time(),
            'to_uid'      =>$uid,
        );
        D('Message')->add($data);
//        $data=array(
//            'content'=>'恭喜您，成功注册为会员！',
//            'title'  =>'恭喜',
//            'type'   =>1,
//            'message_type' =>1,
//            'create_time' =>time(),
//            'to_uid'      =>$uid,
//        );
        $data=array(
            'content'=>'Congratulations , you have been successfully registered as a member！',
            'title'  =>'Congratulations',
            'type'   =>1,
            'message_type' =>1,
            'create_time' =>time(),
            'to_uid'      =>$uid,
        );
        D('Message')->add($data);
    }
    
    
    //获取设计师作品数
    public function getDesignerGoodsNumber($uid){
        //$count=D('GoodsPlatform')->where(array('uid'=>$uid,'status'=>1,'is_sale'=>1))->count();
        $gsmap['gs.online']=1;
        $gsmap['gs.status']=1;
        $gsmap['gp.uid']=$uid;
        $goods_platform_ids=M()->table('yg_goods_store gs')
                        ->join('yg_goods_platform gp on gp.id=gs.goods_platform_id','left')
                        ->where($gsmap)
                        ->field('gs.id,gs.goods_platform_id')
                        ->select();
        if($goods_platform_ids){
            $goods_platform_ids= array_column($goods_platform_ids,'goods_platform_id');
            $goods_platform_ids= array_unique($goods_platform_ids);
        }
        $count=count($goods_platform_ids);
        return $count?$count:0;
    }
    
    
    //获取我的排名
    public function getMyRanking($uid){
        $list=D('AccountLog')->field('uid,sum(user_money) as total_money')->where(array('change_type'=>9))->group('uid')->order('total_money desc')->select();
        $sort=0;
        foreach($list as $k=>$v){
            if($v['uid']==$uid){
                $sort=$k+1;
            }
        }
        if($sort==0){
            $sort=count($list)+1;
        }
        
        return $sort;
    }
    
    
    //获取我的签名
    public function getMySign($uid){
        $sign=$this->where(array('uid'=>$uid))->getField('sign');
        if($sign){
            $signs=explode(',',$sign);
            $sign_color=C('SIGN_COLOR');
            $sign_arr=array();
            foreach($signs as $k=>$v){
                $color_key=array_rand($sign_color);
                $color=$sign_color[$color_key];
                $sign_arr[]=array('color'=>$color,'name'=>$v);
            }
        }
        return $sign_arr?$sign_arr:array();
    }
    
    
    //生成用户邀请码
    public function createInviteCode(){
        $number=getSixNumber(6);
        $has=$this->where(array('invite_code'=>$number))->find();
        if($has){
            $this->createInviteCode();
        }else{
            return $number;
        }
    }
    
    
    
    /**
     根据用户uid获取极光设备ID */
    public function getJpushIdByuid($uid){
        $JpushId=M('Member')->where(array('uid'=>$uid))->getField('jpush_id');
        return $JpushId?$JpushId:false;
    }
    
    public function getJpushIdByuids($uids){
        $map['uid']=array('in',$uids);
        $userlist=M('Member')->field('jpush_id')->where($map)->select();
        $JpushIds=array_filter(array_column($userlist,'jpush_id'));
        return empty($JpushIds)? false:$JpushIds;
    } 
    
    
    public function getUserUids($type=0){
        if($type){
            $map['user_type']=$type;
        }else{
            $map['user_type']=array('in','1,2');
        }
        
        $map['status']=1;
        $uids=$this->where($map)->select();
        $uids=  array_column($uids,'uid');
        return $uids;
    }
    
    
    //判断是否有权利直播/上传视频/发布资讯
    public function checkUserAuth($uid){
        $uinfo=$this->find($uid);
        
        if( $uinfo['user_type']!=2 ){
            return array('error'=>1,'msg'=>'非专业用户不可执行此操作');
        }
        
        if( $uinfo['user_type']==2 && $uinfo['realname_status']!=2 ){
            return array('error'=>1,'msg'=>'您尚未实名认证，不可执行此操作');
        }
        
        return array('error'=>0);
    }
    
    //获取发布者信息
    public function getPublishMan($publish_uid,$uid){
        $uinfo=$this->find($publish_uid);
        $info['uid'] = $uinfo['uid'];
        $info['hx_username'] = $uinfo['hx_username'];
        //$info['hx_password'] = $uinfo['hx_password'];
        $info['nickname'] = $uinfo['nickname'];
        $info['realname'] = $uinfo['realname'];
        $info['head_pic_url'] = getImgUrl($uinfo['head_pic_id']);
        $info['education'] = $uinfo['education'];
        $info['job_title'] = $uinfo['job_title'];
        $info['skill'] = $uinfo['skill'];
        $r=D('Region');
        $info['province_exp']=$r->getName($uinfo['province_id']);
        $info['city_exp']=$r->getName($uinfo['city_id']);
        
        $info['fans_num_exp'] = numberFormat($uinfo['fans_num'],10000,2);
        //$info['fans_num_exp2'] = numberFormat($uinfo['fans_num'],10000,2).'已关注';
        $info['video_num'] = D('Video')->getUserVideoNum($publish_uid);
        $info['article_num'] = D('Article')->getUserArticleNum($publish_uid);
        $info['is_focus'] = D('Focus')->checkHasFocus($uid,1,$info['uid']);
        
        return $info;
    }
    
    public function formatAppList( $list,$uid){
        //如果需要格式化数据，逐条格式化
        if( method_exists($this,'formatApp') ){
                for($i=0;$i<count($list);$i++){
                        if( $list[$i] ){
                                $list[$i] = $this->formatApp( $list[$i] ,$uid);
                        }
                }
        }
        return $list;
    }
    
    function formatApp( $info,$uid){
        if(!$info){
            return $info;
        }
        
        $info['head_pic_url'] = getImgUrl($info['head_pic_id']);
        
        $v=D('Video');
        $a=D('Article');
        $f=D('Focus');
        
        $info['video_num'] = $v->getUserVideoNum($uid);
        
        $info['article_num'] = $a->getUserArticleNum($uid);
        
        $info['is_focus'] = $f->checkHasFocus($uid,1,$info['uid']);
        
        return $info;
    }
    
    
    //是否可升职
    public function checkCanUpToProfession($uid){
        $uinfo = $this->find($uid);
        
        if($uinfo['status']!=1){
            return array('error'=>1,'msg'=>'您的账号已被禁用');
        }
        
        if($uinfo['user_type']!=1){
            return array('error'=>1,'msg'=>'您的身份非普通用户，不可晋升');
        }
        
        return array('error'=>0);
    }

    //分享
    public function getShareInfo($id,$type=0){
        $member_info = $this->find($id);
        
        $info['share_url'] = C('URL_HOST').'/Mobile/Share/professionalDetail/id/'.$id;
        $info['share_title'] = $member_info['realname'].'专家';
        $info['share_content'] = $member_info['skill'];
        $info['share_image_url'] = getImgUrl($member_info['head_pic_id']);
        
        return $info;
    }
    
    
    
    
    //统计
    public function getUserNumber(){
        $map['user_type'] = array('gt',0);
        $map['status'] = array('egt',0);
        
        $num = $this->where( $map )->count();
        
        return $num?$num:0;
    }
    
    
    public function getFamousProfessional(){
        $map['user_type'] = 2;
        $map['realname_status'] = 2;
        $map['status'] = 1;
        $map['fans_num'] = array('gt',0);
        
        $list = $this->where( $map )->order('fans_num desc')->limit(4)->select();
        
        $cr = D('CategoryRecord');
        $pc = D('PlatformCategory');
        
        foreach($list as $k=>$v){
            $list[$k]['head_pic_url'] = getImgUrl($v['head_pic_id']);
            if($v['self_description']){
                $list[$k]['self_description_exp'] = subtext($v['self_description'],60);
            }else if($v['skill']){
                $list[$k]['self_description_exp'] = subtext($v['skill'],60);
            }else{
                $list[$k]['self_description_exp'] = getCName('PROFESSION_TYPE',$v['profession_type']);
            }
            
            $list[$k]['reg_time_exp'] = $v['reg_time']?date('Y.m.d',$v['reg_time']):'';
            //$cate_ids = $cr->getAimCateIds(4,$v['uid']);
            $list[$k]['cate_list'] = $pc->where( array('id'=>array('in',$v['cate_ids'])) )->select();
        }
        
        return $list;
    }
    //
    
    //获取支出总额
    public function getOutAmount($uid){
        $map['uid'] = $uid;
        $map['pay_status'] = 2;
        
        $tip_amount = D('TipOrder')->where( $map )->sum('order_amount');
        $ask_amount = D('AskOrder')->where( $map )->sum('order_amount');
        $lesson_amount = D('LessonOrder')->where( $map )->sum('order_amount');
        
        $total_amount = $tip_amount+$ask_amount+$lesson_amount;
        
        return $total_amount?$total_amount:0;
    }
    //
    
    
    //获取或生成二维码
    public function getUserQrcode($uid){
        $path = C('HTTP_URL').'/Uploads/qrcode/';

        $qinfo = M('qrcode')->where(array('uid'=>$uid))->find();
        if(!$qinfo){
            createQrcode($uid);
            $qinfo = M('qrcode')->where(array('uid'=>$uid))->find();
        }
        // 生成的文件名
        $qrcode = $path.$qinfo['uid'].'_'.$qinfo['level'].'_'.$qinfo['size'].'_'.$qinfo['update_time'].'.png';
        
        return $qrcode;
    }
    
    
    //检查是否可删除
    public function checkCanDel($id){

        
        
        return array('error'=>0);

    }
}
