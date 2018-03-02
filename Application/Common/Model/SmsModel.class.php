<?php
namespace Common\Model;

class SmsModel extends BaseModel{
    
    public function getInfo($map, $field = true) {
        parent::getInfo($map, $field);
    }
    
    //验证短信验证码
    public function checkSmsCode( $mobile ,$code ) {
         $map['mobile'] = $mobile;
         $map['code'] = $code;
         $infos = D('SmsLog')->where($map)->order('create_time desc')->field(true)->limit(1)->select();
         if(empty($infos)){ return false; }
         $past_time = C('SMS_CODE_PAST');
         if( NOW_TIME > ( $infos[0]['create_time'] + $past_time )){  return false;  }
         if($infos[0]['code'] == $code){
            return true;
         }else{
            return false;
         }
    }
    
    /**
     * 防止短信轰炸
     * @author 逆水行舟丶 <316235872@qq.com>
     */    
    public function preventSmsBomb( $mobile ,$time_range = 86400 ,$number = 20) {
        $max_time = NOW_TIME;
        $min_time = NOW_TIME - $time_range;
        $map['create_time']  = array(array('EGT',$min_time),array('ELT',$max_time),'and'); 
        $map['mobile'] = $mobile;
        $SmsLogModel = D('SmsLog');
        //相同手机号码 在某时间段发送超过某数量
        if( $SmsLogModel->where($map)->count() > $number){  return false;  }
        //相同IP在某时间段发送超过某数量
        unset($map['mobile']);
        $map['client_ip'] = get_client_ip(1);
        if( $SmsLogModel->where($map)->count() > $number){  return false;  }
        //手机号码  和 ip 检测通过
        return true;
    }
    
    
    
	
 
}