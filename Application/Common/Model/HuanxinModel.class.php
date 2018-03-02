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
class HuanxinModel extends BaseModel{

    
    public function getRing(){
        $ring_info = $this->find();
        
        $has_now = D('Member')->where( array('hx_username'=>$ring_info['hx_username']) )->find();
        $has_history = D('HuanxinHistory')->where( array('hx_username'=>$ring_info['hx_username']) )->find();
        
        if( $has_now || $has_history ){
            $this->where( array('id'=>$ring_info['id']) )->delete();
            $ring_info=$this->getRing();
        }
        return $ring_info;
    }

    //注册单个环信用户
    public function registerHuanXin(){
        $formgettoken = C('HX_TOKEN_URL')."users";
        $hx_token = $this->getHuanXinToken();
        $user = getHuanXinUsernameAndPwd();
        if(!empty($hx_token)){
            $hx_access_token = "Authorization: Bearer ". $hx_token["access_token"];
            $header = array($hx_access_token);

            $result = _curl_request ( $formgettoken, json_encode($user), $header );
            
            if($result){
                $user['hx_username'] = $user['username'];
                $user['hx_password'] = $user['password'];
                
                $hx_has = $this->where( array('hx_username'=>$user['hx_username']) )->find();
                if( !$hx_has ){
                    $huanxin_res = $this->add( $user );
                }
                if( $huanxin_res ){
                    //echo 'success:'.$user['username']."<script>function myrefresh(){window.location.reload();} setTimeout('myrefresh()',1000);</script>";
                    return $user['username'];
                }
            }else{
                //echo 'error:'.$user['username']."<script>function myrefresh(){window.location.reload();} setTimeout('myrefresh()',1000);</script>";
                return false;
            }
        }else{
            //echo 'error:'.$user['username']."<script>function myrefresh(){window.location.reload();} setTimeout('myrefresh()',1000);</script>";
            return false;
        }
    }

    public function getHuanXinToken(){
        $data = S('hx_token');
        if(empty($data)){
            $url_info =  _get_token(C('HX_TOKEN_URL'),C('HX_CLIENT_ID'),C('HX_CLIENT_SECRET'));
            if($url_info != 'err'){
                $data = $url_info;
                S('hx_token',$url_info,5000000);
            }else{
                echo 'err';
            }
        }
        if(empty($data)){
            return null;
        }else{
            return $data;
        }
    }
    
    //创建环信聊天室
    public function createHuanXinRoom($hx_username,$groupname,$desc){
        $formgettoken = C('HX_TOKEN_URL')."chatrooms";
        $hx_token = $this->getHuanXinToken();
        if(!empty($hx_token)){
            $param['owner'] = $hx_username;
            $param['members'] = array($hx_username);
            $param['maxusers'] = 1000;
            $param['groupname'] = $groupname;
            $param['desc'] = $desc;
            
            $hx_access_token = "Authorization: Bearer ". $hx_token["access_token"];
            $header = array($hx_access_token);
            $result = _curl_request($formgettoken,json_encode($param),$header);
            $result = json_decode($result,true);
            
            if( $result['data'] ){
                return $result['data']['id'];
            }else{
                return false;
            }
            
        }else{
            return false;
        }
        
    }
    
    //删除环信聊天室
    public function closeHuanXinRoom($room_id){
        $formgettoken = C('HX_TOKEN_URL')."chatrooms/".$room_id;
        $hx_token = $this->getHuanXinToken();
        if(!empty($hx_token)){
            $hx_access_token = "Authorization: Bearer ". $hx_token["access_token"];
            $header = array($hx_access_token);
            $result = _curl_request($formgettoken,null,$header,'DELETE');
            $result = json_decode($result,true);
            
            if( $result['data']['success'] ){
                return $result['data']['id'];
            }else{
                return false;
            }
            
        }else{
            return false;
        }
    }
    
    
    //获取所有聊天室
     public function getHuanXinRoom(){
        $formgettoken = C('HX_TOKEN_URL')."chatrooms";
        $hx_token = $this->getHuanXinToken();
        if(!empty($hx_token)){
            $hx_access_token = "Authorization: Bearer ". $hx_token["access_token"];
            $header = array($hx_access_token);
            $result = _curl_request($formgettoken,null,$header,'GET');
            $result = json_decode($result,true);
            
            if( $result['data'] ){
                return $result['data'];
            }else{
                return false;
            }
            
        }else{
            return false;
        }
    }
    
    //获取聊天室
    public function getHuanXinRoomSingle($room_id){
        $formgettoken = C('HX_TOKEN_URL')."chatrooms/".$room_id;
        $hx_token = $this->getHuanXinToken();
        if(!empty($hx_token)){
            $hx_access_token = "Authorization: Bearer ". $hx_token["access_token"];
            $header = array($hx_access_token);
            $result = _curl_request($formgettoken,null,$header,'GET');
            $result = json_decode($result,true);
            
            if( $result['data'] ){
                return $result['data'];
            }else{
                return false;
            }
            
        }else{
            return false;
        }
    }
}
