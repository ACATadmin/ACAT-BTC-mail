
<?php
$ver="2.00";
$amt="100";
$cityno="1000";
$entseq="test";
$bankno="0102";
$merdt=date("Ymd");
$accntno="6222023803013297860";
$orderno=rand(1000000000, 9999999999);
$branchnm="";
$accntnm="宋承宪";
$mobile="13771445322";
$memo="备注";
$mchntcd="0002900F0345142";
$mchntkey="123456";
$reqtype="payforreq";


$xml="<?xml version='1.0' encoding='utf-8' standalone='yes'?><payforreq><ver>".$ver."</ver><merdt>".$merdt."</merdt><orderno>".$orderno."</orderno><bankno>".$bankno."</bankno><cityno>".$cityno."</cityno><accntno>".$accntno."</accntno><accntnm>".$accntnm."</accntnm><branchnm>".$branchnm."</branchnm><amt>".$amt."</amt><mobile>".$mobile."</mobile><entseq>".$entseq."</entseq><memo>".$memo."</memo></payforreq>";

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