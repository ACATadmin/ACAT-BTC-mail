
<?php
$ver="2.00";
$amt="500";
$entseq="test";
$bankno="0305";
$merdt=date("Ymd");
$accntno="6226220615841342";
$orderno=rand(1000000000, 9999999999);
$accntnm="李尚尚";
$mobile="13871445322";
$memo="备注";
$certtp="0";
$certno="320321199008231211";
$projectid="0345142_20170726_603257";
$txncd="09";

$mchntcd="0002900F0345142";
$mchntkey="123456";
$reqtype="sincomeforreq";


$xml="<?xml version='1.0' encoding='utf-8' standalone='yes'?><incomeforreq><ver>".$ver."</ver><merdt>".$merdt."</merdt><orderno>".$orderno."</orderno><bankno>".$bankno."</bankno><accntno>".$accntno."</accntno><accntnm>".$accntnm."</accntnm><amt>".$amt."</amt><certtp>".$certtp."</certtp><certno>".$certno."</certno><mobile>".$mobile."</mobile><entseq>".$entseq."</entseq><memo>".$memo."</memo><projectid>".$projectid."</projectid><txncd>".$txncd."</txncd></incomeforreq>";

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