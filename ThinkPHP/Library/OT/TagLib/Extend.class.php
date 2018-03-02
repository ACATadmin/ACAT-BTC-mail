<?php

namespace OT\TagLib;
use Think\Template\TagLib;

/**
 * 扩展标签
 */
class Extend extends TagLib
{
    /**
     * 定义标签列表
     * @var array
     */
    protected $tags   =  array(
        'authlink'     => array('close' => 1),
    );

    public function _authlink($tag, $content)
    {
        
        $a_regex = '/href\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^"\'>\s]+))/';
        
        $a_res = '';
        preg_match($a_regex, $content, $a_res);
        
        if(empty($a_res)){
            $a_url_regex = '/url\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^"\'>\s]+))/';
            preg_match($a_url_regex, $content, $a_res);
        }
        
        if(empty($a_res)){
            
            return '';
        }
        
        if (0 != substr_count($a_res[1], '?')) {
            
            $arr = explode('?',substr($a_res[1],5));
            
            $arr[1] = substr($arr[1], 0, -2);
            
        }else{
            
            $arr = explode(',', $a_res[1]);
            
            $arr[0] = substr(substr($arr[0],5), 0, -3);
        }
        
        if (1 == substr_count($arr[0], '/')) {
            
            $verify_href = MODULE_NAME.'/'.$arr[0];
        } elseif (0 == substr_count($arr[0], '/')) {
            
            $verify_href = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.$arr[0];
        } else {
            
            $verify_href = $arr[0];
        }
        
        $verify_res = auth_link($verify_href);
        
        if($verify_res){
            
            return $content;
        }
    }
}