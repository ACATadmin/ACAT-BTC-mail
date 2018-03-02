<?php
$data['srcChnl']  ="APP";
$data['busiCd']  =   "AC01";
$data['bankCd']   =  "0105"; 
$data['userNm']   =  "张三"; 
$data['mobileNo']  =  "15846526414";
$data['credtTp']   =  "0";
$data['credtNo']    = "320321199008231211";
$data['acntTp']     =  "01";
$data['acntNo']     =  "6217001140025626059";
$data['mchntCd']     = "0002900F0345142";
$data['isCallback']  = "0";
$data['reserved1']   = "代收签约";
$data['pageFrontUrl'] ="http://demo.rongzhiheng.com/modules/payment/fuyou_qianyue_Return.php";
sort($data,SORT_STRING);    
$str=implode('|', $data);
// var_dump($str);
// exit();
$shi=sha1($str);
$key="123456";
$string=$shi.'|'.$key;
$sign=sha1($string);
echo $sign;
?>