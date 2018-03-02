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
class JpushRecordModel extends BaseModel{

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', 1, self::MODEL_INSERT),
    );

    //新增记录
    public function createLog($uid,$jpush_id,$type,$mini_type=0,$aim_id=0){
        
        $map['uid']=$uid;
        $map['type']=$type;
        $map['mini_type']=$mini_type;
        $map['aim_id']=$aim_id;
        $map['jpush_id']=$jpush_id;
        
        
        $data = $this->create($map);
        if( $data ){
            $ret = $this->add( $data );
        }
        
        
        return $ret;
    }
    
    //判断是否有发送
    public function checkHasPush($uid,$jpush_id,$type,$mini_type=0,$aim_id=0){
        $map['uid']=$uid;
        $map['type']=$type;
        $map['mini_type']=$mini_type;
        $map['aim_id']=$aim_id;
        
        $has = $this->where( $map )->find();
        
        if($has){
            return true;
        }else{
            $this->createLog($uid, $jpush_id, $type, $mini_type, $aim_id);
            return false;
        }
        
    }
    
    
}
