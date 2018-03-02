
<?php
$ver="1.00";
$busicd="AP01";
//$orderno=rand(1000000000, 9999999999);
$orderno='2018011954995357333';
$startdt="20180122";
$enddt="20180123";
$transst="";  //1

$mchntcd="0002900F0345142";
$mchntkey="123456";
$reqtype="qrytransreq";


$xml="<?xml version='1.0' encoding='utf-8' standalone='yes'?><qrytransreq><ver>".$ver."</ver><busicd>".$busicd."</busicd><orderno>".$orderno."</orderno><startdt>".$startdt."</startdt><enddt>".$enddt."</enddt><transst>".$transst."</transst></qrytransreq>";

echo $xml;
echo "\n";
$macsource=$mchntcd."|".$mchntkey."|".$reqtype."|".$xml;
echo $xml;
$mac=md5($macsource);
$mac=strtoupper($mac);
echo $mac;
$list=array("merid"=>$mchntcd,"reqtype"=>$reqtype,"xml"=>$xml,"mac"=>$mac);
$url="https://fht-test.fuiou.com/fuMer/req.do";
echo "\n";
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