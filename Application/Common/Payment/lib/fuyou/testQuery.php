
<?php
$ver ="1.00";
$mchntCd ="0002900F0345178";
$contractNo = "000036227864";
$startdt ="20170626";
$enddt ="20170627";
$mobileNo = "13521233364";
$userNm ="张衍";
$acntNo ="6227002942040547542";
$credtNo = "372929199611103614";

$data['ver']  ="1.00";
$data['mchntCd']  =   "0002900F0345178";
$data['contractNo']   =  "000036227864";
$data['startdt']   =  "20170626";
$data['enddt']  =  "20170627";
$data['mobileNo']   =  "13521233364";
$data['userNm']    = "张衍";
$data['acntNo']     =  "6227002942040547542";
$data['credtNo']     =  "372929199611103614";

sort($data,SORT_STRING);
$str=implode('|', $data);
$shi=sha1($str);
$key="123456";
$string=$shi.'|'.$key;
$signature=sha1($string);
echo $signature;
echo "\n";
$xml="<?xml version='1.0' encoding='utf-8' standalone='yes'?><custmrBusi><ver>".$ver."</ver><mchntCd>".$mchntCd."</mchntCd><contractNo>".$contractNo."</contractNo><startdt>".$startdt."</startdt><enddt>".$enddt."</enddt><userNm>".$userNm."</userNm><credtNo>".$credtNo."</credtNo><acntNo>".$acntNo."</acntNo><mobileNo>".$mobileNo."</mobileNo><signature>".$signature."</signature></custmrBusi>";

echo $xml;
echo "\n";

$list=array("xml"=>$xml);

$url="https://fht-test.fuiou.com/fuMer/api_queryContracts.do";





$query = http_build_query($list);
$options = array(
    'http' => array(
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                    "Content-Length: ".strlen($query)."\r\n".
                    "User-Agent:MyAgent/1.0\r\n",
        'method'  => "POST",
        'content' => $query,
    ),
);
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context, -1, 40000);

echo $result;














?>