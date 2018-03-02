<?php
namespace Common\Sms;

/**
 * 创蓝短信平台
 */
class ClSms extends BaseSms{
    
        Protected $autoCheckFields = false;
        
	//发送信息
        function sendSms($mobile , $content ,$data ,$sms_info){
                $save_content = $this->SmsTplReplace($content,$data);
                $send_content =iconv('UTF-8', 'GBK',$save_content);
                $fields = array(
                    'account' => iconv('GB2312', 'GB2312', $sms_info['username']),
                    'pswd' => iconv('GB2312', 'GB2312', $sms_info['password']),
                    'mobile' => $mobile,
                    'msg' => mb_convert_encoding("$send_content",'UTF-8', 'GB2312')
                );
                
                $url=$sms_info['server_url'];
                $o="";
                foreach ($fields as $k=>$v){
                   $o.= "$k=".urlencode($v)."&";
                }
                $fields=substr($o,0,-1);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                $result = curl_exec($ch);
                
                $result_arr = explode(',', $result);
               if($result_arr[1] == 0){
                   $res_data['sms_res'] = 0;
               }else{
                   $res_data['sms_res'] = 50011;
               }
                $res_data['mobile']  = $mobile;
                $res_data['content'] = $save_content;
                $res_data['code'] = $data['var_code'];
                return $res_data;
	}
}