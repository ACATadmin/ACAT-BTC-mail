<?php
namespace Common\Sms;

/**
 * 优信短信平台
 */
class YxSms extends BaseSms{
    
        Protected $autoCheckFields = false;
        
	//发送信息
        function sendSms($mobile , $content ,$data ,$sms_info){
                $send_content = $this->SmsTplReplace($content,$data);
                $fields = array(
                    'CorpID' => urlencode($sms_info['username']),
                    'Pwd' => urlencode($sms_info['password']),
                    'mobile' => urlencode($mobile),
                    'Content' => iconv('UTF-8', 'GBK', $send_content),
                    'Cell' => '',
                    'SendTime' => ''
                );
                $result=execPostRequest($sms_info['server_url'], $fields);
                $res_data['sms_res'] = $result;
                $res_data['mobile']  = $mobile;
                $res_data['content'] = $send_content;
                $res_data['code'] = $data['var_code'];
                return $res_data;
	}
}