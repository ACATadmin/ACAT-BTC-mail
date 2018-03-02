<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Common\Model;


/**
 * 文档基础模型
 */
class JpushModel extends BaseModel{

    protected $autoCheckFields = false;
    
    
    
    //强制下线推送
    public function underLinePush($old_jpush_id,$uid){
        
        $msg['type']=1;
        $msg['uid']=$uid;
        $msg['msg']='您已在其它设备登录';
        $param=  json_encode($msg);
        
        $content='您已在其它设备登录';
        
        $result_s = sendSpecialMsgJson($old_jpush_id, $content,$param);
    }
    
    //强制下线后台
    public function platformUnderLinePush($uid){
        $jpushid = D('Member')->getJpushIdByuid($uid);
        
        if($jpushid){
            $msg['type']=1;
            $msg['uid']=$uid;
            $msg['msg']='您已被强制下线';
            $param=  json_encode($msg);

            $content='您已被强制下线';

            $result_s = sendSpecialMsgJson($jpushid, $content,$param);
        }
        
    }
    
    //实名认证审核
    public function realnamePush($id,$type,$fail_reason){
        $jpushid = D('Member')->getJpushIdByuid($id);
        if($jpushid){
            $msg['type'] = 2;
            $msg['uid'] = $id;
            $msg['mini_type'] = $type;
            $param=  json_encode($msg);
            
            switch($type){
                case 1:
                    $content = '您的实名认证已通过审核';
                    break;
                case 2:
                    $content = '您的实名认证未通过，'.$fail_reason;
                    break;
            }
            
            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
        }
    }
    
    //视频审核
    public function videoCheckPush($id,$type,$fail_reason){
        $v_info = D('Video')->find($id);
        $jpushid = D('Member')->getJpushIdByuid($v_info['uid']);
        if($jpushid){
            $msg['type'] = 3;
            $msg['uid'] = $v_info['uid'];
            $msg['video_id'] = $id;
            $msg['mini_type'] = $type;
            $param=  json_encode($msg);
            
            switch($type){
                case 1:
                    $content = '您上传的视频《'.$v_info['title'].'》已通过审核';
                    break;
                case 2:
                    $content = '您上传的视频《'.$v_info['title'].'》未通过审核，'.$fail_reason;
                    break;
            }
            
            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
        }
    }
    
    //资讯审核
    public function articleCheckPush($id,$type,$fail_reason){
        $a_info = D('Article')->find($id);
        $jpushid = D('Member')->getJpushIdByuid($a_info['uid']);
        if($jpushid){
            $msg['type'] = 4;
            $msg['uid'] = $a_info['uid'];
            $msg['article_id'] = $id;
            $msg['mini_type'] = $type;
            $param=  json_encode($msg);
            
            switch($type){
                case 1:
                    $content = '您发布的资讯《'.$a_info['title'].'》已通过审核';
                    break;
                case 2:
                    $content = '您发布的资讯《'.$a_info['title'].'》未通过审核，'.$fail_reason;
                    break;
            }
            
            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
        }
    }
    
    //发布直播预告成功提醒
    public function publishLiveNoticePush($id){
        $l_info = D('Live')->find($id);
        $jpushid = D('Member')->getJpushIdByuid($l_info['uid']);
        if($jpushid){
            $msg['type'] = 5;
            $msg['uid'] = $l_info['uid'];
            $msg['live_id'] = $id;
            $param=  json_encode($msg);
            
            
            $content = '您发布的直播《'.$l_info['title'].'》预告已发布成功，请留意直播时间。';
            
            
            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
        }
    }
    
//    //直播即将开始通知收藏用户
//    public function liveStartSoonPush($id,$uid){
//        $l_info = D('Live')->find($id);
//        $jpushid = D('Member')->getJpushIdByuid($uid);
//        
//        //判断是否已发送
//        if($jpushid){
//            $has_push = D('JpushRecord')->checkHasPush($uid,$jpushid,6,0,$id);
//            if($has_push){
//                return false;
//            }
//        }
//        //
//        
//        
//        if($jpushid){
//            $msg['type'] = 6;
//            $msg['uid'] = $uid;
//            $msg['live_id'] = $id;
//            $param=  json_encode($msg);
//            
//            
//            $content = '您收藏的直播《'.$l_info['title'].'》即将开始。';
//            
//            
//            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
//        }
//    }
    
    //直播即将开始通知收藏用户
    public function liveStartSoonPush($id,$uid){
        $l_info = D('Live')->find($id);
        $jpushid = D('Member')->getJpushIdByuid($uid);
        
        //判断是否已发送
        if($jpushid){
            $has_push = D('JpushRecord')->checkHasPush($uid,$jpushid,6,0,$id);
            if($has_push){
                return false;
            }
        }
        //
        
        
        if($jpushid){
            $msg['type'] = 6;
            $msg['uid'] = $uid;
            $msg['live_id'] = $id;
            $param=  json_encode($msg);
            
            
            $content = '您的直播《'.$l_info['title'].'》即将开始。';
            
            
            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
        }
    }
    
    //直播开始通知收藏用户
    public function liveStartPush($id){
        $l_info = D('Live')->find($id);
        
        $map['fc.type'] = 1;
        $map['fc.relevance_type'] = 1;
        $map['fc.aim_id'] = $id;
        $map['l.type'] = 2;
        $map['l.live_status'] = 1;
        $map['l.status'] = 1;

        $list = D('FocusCollect fc')->join('ysyy_live l on fc.aim_id=l.id','left')
                                    ->where( $map )
                                    ->field('fc.*')
                                    ->select();
        
        if( $list ){
            
            $content = '您收藏的直播《'.$l_info['title'].'》已经开始。';
            
            foreach($list as $k=>$v){
                $jpushid = D('Member')->getJpushIdByuid($v['uid']);
                if($jpushid){
                    $msg['type'] = 7;
                    $msg['uid'] = $v['uid'];
                    $msg['live_id'] = $id;
                    $param=  json_encode($msg);

                    $result_s = sendSpecialMsgJson($jpushid, $content, $param);
                }
            }
        }
    }
    
    
    //问询通知
    public function newAskPush($ask_id){
        $a_info = D('Ask')->find($ask_id);
        $jpushid = D('Member')->getJpushIdByuid($a_info['ask_uid']);
        if($jpushid){
            $msg['type'] = 8;
            $msg['uid'] = $a_info['uid'];
            $msg['ask_uid'] = $a_info['ask_uid'];
            $msg['ask_id'] = $ask_id;
            $param=  json_encode($msg);
            
            
            $content = '您有新的问询';
            
            
            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
        }
    }
    
    
    //问询结束通知
    public function endAskPush($ask_id){
        $a_info = D('Ask')->find($ask_id);
        $jpushid = D('Member')->getJpushIdByuid($a_info['uid']);
        if($jpushid){
            $msg['type'] = 9;
            $msg['uid'] = $a_info['uid'];
            $msg['ask_id'] = $ask_id;
            $param=  json_encode($msg);
            
            
            $content = '您发起的问询专家已结束';
            
            
            $result_s = sendSpecialMsgJson($jpushid, $content, $param);
        }
    }
}
