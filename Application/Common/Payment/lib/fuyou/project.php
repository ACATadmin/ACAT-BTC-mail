
<?php
$orderno=rand(1000000000, 9999999999);
$ssn=substr(strval(rand(1000000, 1999999)), 1,6);
$merid="0002900F0345142";
$key="123456";
$xml="<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?>"
					. "<project>"
					. "<ver>2.00</ver>"
					. "<orderno>".$orderno."</orderno>"
					. "<mchnt_nm>sj</mchnt_nm>"
					. "<project_ssn>".$ssn."</project_ssn>"
					. "<project_amt>50000000</project_amt>"
					. "<expected_return>36.00</expected_return>"
					. "<project_fee>4.00</project_fee>"
					. "<contract_nm>26872576960310567000</contract_nm>"
					. "<project_deadline>7</project_deadline>"
					. "<raise_deadline>0</raise_deadline>"
					. "<max_invest_num></max_invest_num>"
					. "<min_invest_num></min_invest_num>"
					. "<bor_nm>小牲口</bor_nm>"
					. "<id_tp>0</id_tp>"
					. "<id_no>320321199008231211</id_no>"
					. "<card_no>6226220615841342</card_no>"
					. "<mobile_no>13771445322</mobile_no>"
					. "</project>";

echo $xml;
$macsource=$merid."|".$key."|".$xml;
echo $xml;
$mac=md5($macsource);
$mac=strtoupper($mac);
echo $mac;
$list=array("merid"=>$merid,"xml"=>$xml,"mac"=>$mac);
$url="https://fht-test.fuiou.com/fuMer/inspro.do";
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