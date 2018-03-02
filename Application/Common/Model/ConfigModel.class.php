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
 * 配置基础模型
 */
class ConfigModel extends BaseModel{

    //赞赏红包
    public function getTipRedPacket(){
        $packets = C('TIP_RED_PACKET');
        return $packets;
    }
    
    //问询价格建议
    public function getAskPriceNotice($uid){
        $ask_price_notice = C('ASK_PRICE_NOTICE');
        $notice = '';
        
        $uinfo = D('Member')->find($uid);
        if($uinfo['job_title']){
            $notice_price = $ask_price_notice[$uinfo['job_title']];
            if($notice_price){
                $notice = '您是'.$uinfo['job_title'].'级别,建议问询价格区间为：'.$notice_price.'元/次';
            }
        }
        
        return $notice;
    }
}
