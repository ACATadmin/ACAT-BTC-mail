<?php
namespace Common\Sms;
use Think\Model;

/**
 * 短信公共模型
 */
class BaseSms extends Model{
 
        //短信模板变量替换
        function SmsTplReplace($content,$data = array('var_code'=> '123456')){
            foreach ($data as $key => $value){
                $content = str_replace($key, $value, $content);
            }
            return $content;            
        }
}
