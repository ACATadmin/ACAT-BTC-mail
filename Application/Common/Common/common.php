<?php 

/**
 * 对多为数组排序
 * @param  arr $arrays     源数组
 * @param  string $sort_key   根据哪个字段进行排序
 * @param  排序方式 $sort_order 升序/降序
 * @param  排序字段的数据类型 $sort_type  数字 字符串。。
 * @return arr             排序后的数组
 */
function multi_array_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC){
	if(is_array($arrays)){
		foreach ($arrays as $array){
			if(is_array($array)){
				$key_arrays[] = $array[$sort_key];
			}else{
				return false;
			}
		}
	}else{
		return false;
	}
	array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
	return $arrays;
}

//保存文件
function sf($arr,$fpath='D:\p.php'){
	$data = "<?php\nreturn ".var_export($arr, true).";\n?>";
	file_put_contents($fpath,$data);
}


//
function getOrderSn(){
	/*
	$orderList = D('Payment')->getAllPaymentOrderSnName();

	$count = count( $orderList );
	for($i=0;$i<$count;$i++){
		if( I($orderList[$i]) ){
			return I($orderList[$i]);
		}
	}
	
	//部分通知不是以POST及GRT方式返回的，需要依据不同支付方式对象获取订单号
	
	return null;
	*/
	return D('Payment')->getOrderSn();	
}
function getPayCode( $pay_id ){
	return D('Payment')->where( array('pay_id'=>$pay_id) )->getField('pay_code');
}

/*
 *遍历打印数组 
 */
function vd($arr){
	echo "<pre>";
	var_dump($arr);
	echo "</pre>";
}

function vde($arr){
	vd($arr);
	exit;
}

function ee($str){
	echo $str;exit;
}



function GetIP(){
	$ip = $_SERVER["REMOTE_ADDR"];
	/*
	//测试模拟本地IP
	if( $ip = '127.0.0.1' ){
		$ip = '58.198.91.76';
	}
	*/
	return $ip;
}

//从指定数组中提取某个字段组成新的索引数组
function getIdArr( $arr ,$idName='id'){
     $count 	= count($arr);
     $IdArr 	= array();
     for($i=0;$i<$count;$i++){
     	$IdArr[] = $arr[$i][$idName];
     }
     return $IdArr;
}

//从指定数组中提取某个字段为索引组成关联数组
function getIdIndexArr( $arr ,$idName='id'){
	$count 	= count($arr);
	$IdArr 	= array();
	for($i=0;$i<$count;$i++){
		$IdArr[ $arr[$i][$idName] ] = $arr[$i];
	}
	return $IdArr;
}
function getIdIndexSubArr( $arr ,$idName='id'){
	$count 	= count($arr);
	$IdArr 	= array();
	for($i=0;$i<$count;$i++){
		$IdArr[ $arr[$i][$idName] ][] = $arr[$i];
	}
	return $IdArr;
}


/**********
 * 发送邮件 *
**********/
function SendMail($address,$title,$message,$isHtml=0)
{
	import('Vendor.Mail.phpmailer');
	$mail=new \Verdor\Mail\PHPMailer();
	// 设置PHPMailer使用SMTP服务器发送Email
	$mail->IsSMTP();

	// 设置邮件的字符编码，若不指定，则为'UTF-8'
	$mail->CharSet='UTF-8';

	// 添加收件人地址，可以多次使用来添加多个收件人
	$mail->AddAddress($address);

	// 设置邮件正文
	$mail->Body=$message;

	// 设置邮件头的From字段。
	$mail->From=C('MAIL_ADDRESS');

	// 设置发件人名字
	$mail->FromName=C('SERVICE_EMAIL');

	// 设置邮件标题
	$mail->Subject=$title;

	// 设置SMTP服务器。
	$mail->Host=C('MAIL_SMTP');

	// 设置为"需要验证"
	$mail->SMTPAuth=true;
	
	//
	//$mail->IsHTML=true;
	//$mail->AltBody ="text/html";
	
	
	if( $isHtml ){				//发送HTML内容
		$mail->IsHTML=true;
		$mail->AltBody ="text/html";
	}
	
	
	// 设置用户名和密码。
	$mail->Username=C('MAIL_LOGINNAME');
	$mail->Password=C('MAIL_PASSWORD');

	// 发送邮件。
	return($mail->Send());
}


function getFirstLetter($str)
{
	$asc=ord(substr($str,0,1));
	if ($asc<160) //非中文
	{
		if ($asc>=48 && $asc<=57){
			return '1'; //数字
		} elseif ($asc>=65 && $asc<=90){
			return chr($asc);   // A--Z
		} elseif ($asc>=97 && $asc<=122){
			return chr($asc-32); // a--z
		} else{
			return '~'; //其他
		}
	}
	else   //中文
	{
		$asc=$asc*1000+ord(substr($str,1,1));
		//获取拼音首字母A--Z
		if ($asc>=176161 && $asc<176197){
			return 'A';
		} elseif ($asc>=176197 && $asc<178193){
			return 'B';
		} elseif ($asc>=178193 && $asc<180238){
			return 'C';
		} elseif ($asc>=180238 && $asc<182234){
			return 'D';
		} elseif ($asc>=182234 && $asc<183162){
			return 'E';
		} elseif ($asc>=183162 && $asc<184193){
			return 'F';
		} elseif ($asc>=184193 && $asc<185254){
			return 'G';
		} elseif ($asc>=185254 && $asc<187247){
			return 'H';
		} elseif ($asc>=187247 && $asc<191166){
			return 'J';
		} elseif ($asc>=191166 && $asc<192172){
			return 'K';
		} elseif ($asc>=192172 && $asc<194232){
			return 'L';
		} elseif ($asc>=194232 && $asc<196195){
			return 'M';
		} elseif ($asc>=196195 && $asc<197182){
			return 'N';
		} elseif ($asc>=197182 && $asc<197190){
			return 'O';
		} elseif ($asc>=197190 && $asc<198218){
			return 'P';
		} elseif ($asc>=198218 && $asc<200187){
			return 'Q';
		} elseif ($asc>=200187 && $asc<200246){
			return 'R';
		} elseif ($asc>=200246 && $asc<203250){
			return 'S';
		} elseif ($asc>=203250 && $asc<205218){
			return 'T';
		} elseif ($asc>=205218 && $asc<206244){
			return 'W';
		} elseif ($asc>=206244 && $asc<209185){
			return 'X';
		} elseif ($asc>=209185 && $asc<212209){
			return 'Y';
		} elseif ($asc>=212209){
			return 'Z';
		} else{
			return '~';
		}
	}
}


function getfirstchar($s0){
	$fchar = ord($s0{0});
	if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
	$s1 = iconv("UTF-8","gb2312", $s0);
	$s2 = iconv("gb2312","UTF-8", $s1);
	if($s2 == $s0){$s = $s1;}else{$s = $s0;}
	$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	if($asc >= -20319 and $asc <= -20284) return "A";
	if($asc >= -20283 and $asc <= -19776) return "B";
	if($asc >= -19775 and $asc <= -19219) return "C";
	if($asc >= -19218 and $asc <= -18711) return "D";
	if($asc >= -18710 and $asc <= -18527) return "E";
	if($asc >= -18526 and $asc <= -18240) return "F";
	if($asc >= -18239 and $asc <= -17923) return "G";
	if($asc >= -17922 and $asc <= -17418) return "I";
	if($asc >= -17417 and $asc <= -16475) return "J";
	if($asc >= -16474 and $asc <= -16213) return "K";
	if($asc >= -16212 and $asc <= -15641) return "L";
	if($asc >= -15640 and $asc <= -15166) return "M";
	if($asc >= -15165 and $asc <= -14923) return "N";
	if($asc >= -14922 and $asc <= -14915) return "O";
	if($asc >= -14914 and $asc <= -14631) return "P";
	if($asc >= -14630 and $asc <= -14150) return "Q";
	if($asc >= -14149 and $asc <= -14091) return "R";
	if($asc >= -14090 and $asc <= -13319) return "S";
	if($asc >= -13318 and $asc <= -12839) return "T";
	if($asc >= -12838 and $asc <= -12557) return "W";
	if($asc >= -12556 and $asc <= -11848) return "X";
	if($asc >= -11847 and $asc <= -11056) return "Y";
	if($asc >= -11055 and $asc <= -10247) return "Z";
	return null;
}

function pinyin($zh){
	$ret = "";
	
	//echo $zh.'--';
	
	$s1 = iconv("UTF-8","gb2312", $zh);
	$s2 = iconv("gb2312","UTF-8", $s1);
	
	if($s2 == $zh){$zh = $s1;}
	
	
	
	for($i = 0; $i < strlen($zh); $i++){
		$s1 = substr($zh,$i,1);
		$p = ord($s1);
		
		//echo $p.'---'.$s1.'---'.$zh."<br/>";
		
		if($p > 160){
			$s2 = substr($zh,$i++,2);
			$ret .= getfirstchar($s2);
		}else{
			$ret .= $s1;
		}
	}
	return $ret;
}












function getinitial($str)
{
	$asc=ord(substr($str,0,1)); //ord()获取ASCII

	if ($asc<160) //非中文
	{
		if ($asc>=48 && $asc<=57){
			return '1'; //数字
		}elseif ($asc>=65 && $asc<=90){
			return chr($asc); // A--Z chr将ASCII转换为字符
		}elseif ($asc>=97 && $asc<=122){
			return chr($asc-32); // a--z
		}else{
			return '~'; //其他
		}
	}
	else //中文
	{
		$asc=$asc*1000+ord(substr($str,1,1));
		//获取拼音首字母A--Z
		if ($asc>=176161 && $asc<176197){
			return 'A';
		}elseif ($asc>=176197 && $asc<178193){
			return 'B';
		}elseif ($asc>=178193 && $asc<180238){
			return 'C';
		}elseif ($asc>=180238 && $asc<182234){
			return 'D';
		}elseif ($asc>=182234 && $asc<183162){
			return 'E';
		}elseif ($asc>=183162 && $asc<184193){
			return 'F';
		}elseif ($asc>=184193 && $asc<185254){
			return 'G';
		}elseif ($asc>=185254 && $asc<187247){
			return 'H';
		}elseif ($asc>=187247 && $asc<191166){
			return 'J';
		}elseif ($asc>=191166 && $asc<192172){
			return 'K';
		}elseif ($asc>=192172 && $asc<194232){
			return 'L';
		}elseif ($asc>=194232 && $asc<196195){
			return 'M';
		}elseif ($asc>=196195 && $asc<197182){
			return 'N';
		}elseif ($asc>=197182 && $asc<197190){
			return 'O';
		}elseif ($asc>=197190 && $asc<198218){
			return 'P';
		}elseif ($asc>=198218 && $asc<200187){
			return 'Q';
		}elseif ($asc>=200187 && $asc<200246){
			return 'R';
		}elseif ($asc>=200246 && $asc<203250){
			return 'S';
		}elseif ($asc>=203250 && $asc<205218){
			return 'T';
		}elseif ($asc>=205218 && $asc<206244){
			return 'W';
		}elseif ($asc>=206244 && $asc<209185){
			return 'X';
		}elseif ($asc>=209185 && $asc<212209){
			return 'Y';
		}elseif ($asc>=212209){
			return 'Z';
		}else{
			return '~';
		}
	}
}

//获取当前完整URL
function get_urls() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

//获取当前完整URL
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}


//验证手机号是否正确
function is_mobile($tel)   
{   
	if(strlen($tel) == "11")
	{
		preg_match_all("/^0?(13[0-9]|15[0-9]|18[0-9]|17[0-9]|14[57])[0-9]{8}$/",$tel,$array);	
		// preg_match_all("/13[123569]{1}\d{8}|15[1235689]\d{8}|17[1235689]\d{8}|14[1235689]\d{8}|188\d{8}/",$tel,$array);
		
		//	/^13[0-9]{1}[0-9]{8}$|14[57])[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/
		// /^0?(13[0-9]|15[012356789]|18[02356789]|14[57])[0-9]{8}$/; 
		
		
		//vde($array);
		
		return count($array[0]);	//符合条件的手机号数量	
	}else{
		return 0;
	}	
}

//验证是否为邮箱
function is_valid_email($email, $test_mx = false)
{
	if(eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))
	if($test_mx)
	{
		list($username, $domain) = split("@", $email);
		return getmxrr($domain, $mxrecords);
	}
	else
		return true;
	else
		return false;
}



//获取操作系统
function getOS(){
	global $_SERVER;
	$Agent = $_SERVER['HTTP_USER_AGENT'];

	$browserplatform='';
	
	if (eregi('win',$Agent) && strpos($Agent, '95')) {
		$browserplatform="Windows 95";
	}
	elseif (eregi('win 9x',$Agent) && strpos($Agent, '4.90')) {
		$browserplatform="Windows ME";
	}
	elseif (eregi('win',$Agent) && ereg('98',$Agent)) {
		$browserplatform="Windows 98";
	}
	elseif (eregi('win',$Agent) && eregi('nt 5.0',$Agent)) {
		$browserplatform="Windows 2000";
	}
	elseif (eregi('win',$Agent) && eregi('nt 5.1',$Agent)) {
		$browserplatform="Windows XP";
	}
	elseif (eregi('win',$Agent) && eregi('nt 6.0',$Agent)) {
		$browserplatform="Windows Vista";
	}
	elseif (eregi('win',$Agent) && eregi('nt 6.1',$Agent)) {
		$browserplatform="Windows 7";
	}
	elseif (eregi('win',$Agent) && ereg('32',$Agent)) {
		$browserplatform="Windows 32";
	}
	elseif (eregi('win',$Agent) && eregi('nt',$Agent)) {
		$browserplatform="Windows NT";
	}elseif (eregi('Mac OS',$Agent)) {
		$browserplatform="Mac OS";
	}
	elseif (eregi('linux',$Agent)) {
		$browserplatform="Linux";
	}
	elseif (eregi('unix',$Agent)) {
		$browserplatform="Unix";
	}
	elseif (eregi('sun',$Agent) && eregi('os',$Agent)) {
		$browserplatform="SunOS";
	}
	elseif (eregi('ibm',$Agent) && eregi('os',$Agent)) {
		$browserplatform="IBM OS/2";
	}
	elseif (eregi('Mac',$Agent) && eregi('PC',$Agent)) {
		$browserplatform="Macintosh";
	}
	elseif (eregi('PowerPC',$Agent)) {
		$browserplatform="PowerPC";
	}
	elseif (eregi('AIX',$Agent)) {
		$browserplatform="AIX";
	}
	elseif (eregi('HPUX',$Agent)) {
		$browserplatform="HPUX";
	}
	elseif (eregi('NetBSD',$Agent)) {
		$browserplatform="NetBSD";
	}
	elseif (eregi('BSD',$Agent)) {
		$browserplatform="BSD";
	}
	elseif (ereg('OSF1',$Agent)) {
		$browserplatform="OSF1";
	}
	elseif (ereg('IRIX',$Agent)) {
		$browserplatform="IRIX";
	}
	elseif (eregi('FreeBSD',$Agent)) {
		$browserplatform="FreeBSD";
	}
	if ($browserplatform=='') {$browserplatform = "Unknown"; }
	return $browserplatform;
}

function getBrowser() {  
    $BrowserArr = getBrowserArr();
    return $BrowserArr[0]." ".$BrowserArr[1];
} 
function getBrowserArr( $VERSION_ROUNDDOWN = 0 ) {
	global $_SERVER;  
    $Agent  = $_SERVER['HTTP_USER_AGENT'];  
    
    $browseragent="";   //浏览器
    $browserversion=""; //浏览器的版本
    if (ereg('MSIE ([0-9].[0-9]{1,2})',$Agent,$version)) {
    	$browserversion=$version[1];
    	$browseragent="IE";
    } else if (ereg( 'Opera/([0-9]{1,2}.[0-9]{1,2})',$Agent,$version)) {
    	$browserversion=$version[1];
    	$browseragent="Opera";
    } else if (ereg( 'Firefox/([0-9.]{1,5})',$Agent,$version)) {
    	$browserversion=$version[1];
    	$browseragent="Firefox";
    }else if (ereg( 'Chrome/([0-9.]{1,3})',$Agent,$version)) {
    	$browserversion=$version[1];
    	$browseragent="Chrome";
    }
    else if (ereg( 'Safari/([0-9.]{1,3})',$Agent,$version)) {
    	$browseragent="Safari";
    	$browserversion="";
    }
    else {
    	$browserversion="";
    	$browseragent="Unknown";
    }
    
    if( $VERSION_ROUNDDOWN ){
    	$browserversion = floor($browserversion);
    }
    
    return array($browseragent,$browserversion);
} 



/**
 * 列出目录下的所有文件
 *
 * @param str $path 目录
 * @param str $exts 后缀
 * @param array $list 路径数组
 * @return array 返回路径数组
 */
function dir_list($path, $exts = '', $list = array()) {
	$path = dir_path($path);
	$files = glob($path . '*');
	foreach($files as $v) {
		if (!$exts || preg_match("/\.($exts)/i", $v)) {
			$list[] = $v;
			if (is_dir($v)) {
				$list = dir_list($v, $exts, $list);
			}
		}
	}
	return $list;
}
function dir_path($path) {
	$path = str_replace('\\', '/', $path);
	if (substr($path, -1) != '/') $path = $path . '/';
	return $path;
}
//获取目录下所有文件
function file_list($path){
	$file=scandir($path);
	foreach ($file as $k=>$v){
		if( is_dir($path.'/'.$v) )unset( $file[$k] );
	}
	return array_values($file);
}

function isMobile()
{
	
	//ee($_SERVER['HTTP_USER_AGENT']);
	if(strpos($_SERVER['HTTP_USER_AGENT'],"iPad")){		//pad直接显示PC界面
		return false;
	}
	
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
		return true;
	}
	// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset ($_SERVER['HTTP_VIA']))
	{
		// 找不到为flase,否则为true
		return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	}
	// 脑残法，判断手机发送的客户端标志,兼容性有待提高
	if (isset ($_SERVER['HTTP_USER_AGENT']))
	{
		$clientkeywords = array ('nokia',
				'sony',
				'ericsson',
				'mot',
				'samsung',
				'htc',
				'sgh',
				'lg',
				'sharp',
				'sie-',
				'philips',
				'panasonic',
				'alcatel',
				'lenovo',
				'iphone',
				'ipod',
				'blackberry',
				'meizu',
				'android',
				'netfront',
				'symbian',
				'ucweb',
				'windowsce',
				'palm',
				'operamini',
				'operamobi',
				'openwave',
				'nexusone',
				'cldc',
				'midp',
				'wap',
				'mobile'
		);
		// 从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
		{
			return true;
		}
	}
	// 协议法，因为有可能不准确，放到最后判断
	if (isset ($_SERVER['HTTP_ACCEPT']))
	{
		// 如果只支持wml并且不支持html那一定是移动设备
		// 如果支持wml和html但是wml在html之前则是移动设备
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
		{
			return true;
		}
	}
	return false;
}
//---------------------------------------------------------------------------------------------------------
//必须登录
function must_login(){
	if( !is_login() ){	//未登录用户，记录当前页面URL，跳转到登录页，待用户登录成功后回跳到当前页面
		$come_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
		cookie('jumpTo',$come_url,180);
		//session($name,null);
		//cookie('name','value',180);
		redirect( U('User/login') );
	}
}
//记录回跳地址
function saveJumpTo(){
	//$come_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	
	$come_url = $_SERVER['HTTP_REFERER'];
	//echo $come_url;
	
	if( check_jump_url( $come_url )==false ){
		return false;
	}
	
	//echo $come_url;
		
	//
	cookie('jumpTo',$come_url,180);
}

function check_jump_url( $come_url ){
	//将注册登录两个页面排除在外
	$is_login 			= strpos($come_url,'login');
	$is_logout 			= strpos($come_url,'logout');
	$is_register 		= strpos($come_url,'register');
	$is_resetPassword 	= strpos($come_url,'resetPassword');
	$is_addons 			= strpos($come_url,'Addons');
	
	if( $is_login || $is_register || $is_resetPassword || $is_logout || $is_addons ){
		return false;
	}
	return true;
}

function is_logout_from_ucenter(){
	$come_url = $_SERVER['HTTP_REFERER'];
	
	if( strpos($come_url,'Account') ){
		return true;
	}
	return false;
}

//登录后跳转到的页面
function login_jump_url(){
	
	$jumpTo = cookie('jumpTo');
	
	if( check_jump_url( $jumpTo )==false ){
		$jumpTo = '';
	}
	return $jumpTo?$jumpTo:U('Account/baseInfo');
	
	//$jumpTo = instant('jumpTo');
	//return $jumpTo?$jumpTo:U('Account/baseInfo');
}
//---------------------------------------------------------------------------------------------------------


//------------thinkox提取--------------
function is_ie()
{
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	$pos = strpos($userAgent, ' MSIE ');
	if ($pos === false) {
		return false;
	} else {
		return true;
	}
}

//通过uid获取用户名，如果未设置真实姓名，以用户名为准，设置了，以真实姓名为准	昵称、手机号、邮箱、用户名
function get_username_app( $uid ){
	$model = M();
	$res = $model	->table( C('DB_PREFIX').'member as m')
				->join( C('DB_PREFIX').'ucenter_member AS um ON m.uid=um.id',left)
				->field('m.nickname,um.username,um.email,um.mobile')
				->where('m.uid='.$uid)
				->find();
	//echo $model->getLastSql();
	//清空临时邮箱
	//$res['email'] = empty_default_email( $res['email'] );
	//vde($res);
	
	if( !empty($res['nickname']) ){							//用户名、分配帐号
		return 	$res['nickname'];
	}else if( !empty($res['username']) ){		//昵称
		return $res['username'];
	}else if( !empty($res['mobile']) ){						//手机号
		return substr_replace($res['mobile'],'****',3,4);
		//return 	$res['mobile_phone'];
	}else if( !empty($res['email']) ){								//邮箱
		return 	$res['email'];
	}else{
		// return "匿名";
		return "";
	}
}




function create_new_username(){
	
	$username = rand(1000000, 9999999);		//随机生成一个用户名
	
	return $username;
}




function get_mail_temp( $content, $replace ){
	
	//为邮件内的图片地址加上域名
	$content = str_replace('src="/','src="http://'.$_SERVER['HTTP_HOST'].'/',$content);
	
	$replace['datetime'] = ($replace['datetime'])?$replace['datetime']:date('Y-m-d H:i:s');
	$replace['date'] 	 = ($replace['date'])?$replace['date']:date('Y-m-d');
	
	//替换模版中的变量
	$content = str_replace('##username##'		,$replace['username'] 	, $content);	
	$content = str_replace('##check_url##'		,'<a href="'.$replace['check_url'].'">'.$replace['check_url'].'</a>' , $content);
	$content = str_replace('##email##'			,$replace['email']		, $content);
	$content = str_replace('##datetime##'		,$replace['datetime']	, $content);
	$content = str_replace('##date##'			,$replace['date']		, $content);
	
	$content = str_replace('##project_name##'	,$replace['project_name']		, $content);
	$content = str_replace('##project_url##'	,$replace['project_url']		, $content);
	
	return $content;
}

function strcomp($str1,$str2){
	if($str1 == $str2){
		return TRUE;
	}else{
		return FALSE;
	}
}
//
function checkDomain( $jumpMainUrl ){
	$SERVER_NAME = $_SERVER['SERVER_NAME'];
	
	if( !strcomp($SERVER_NAME,'www.'.$jumpMainUrl) && !strcomp($SERVER_NAME,'m.'.$jumpMainUrl) ){
		redirect( 'http://www.'.$jumpMainUrl.$_SERVER['REQUEST_URI'] );
	}
}




function get_c_title($id,$c_name,$p_name='title'){
	$C = C( $c_name );
	$ret = $C[$id][$p_name];
	
	if( !$ret && $p_name=='title' )
		$ret = $C[$id]['explain'];
	if( !$ret && $p_name=='title' )
		$ret = $C[$id]['name'];
	
	return $ret;
}

function yes_no_title($id){
	return $id?'是':'否';
}






/**
 *
 * @param unknown $msg
 * @param unknown $mobile
 * @return string  调用成功后将返回一个大于零的整数字符串，表示成功提交的短信数量。例如返回2000，标识从第1条到第2000条都成功提交。
 * 失败返回标识：
 *   -1,用户ID或者密码错误
     -2,缺少必要参数
     -3,含有不合法参数
     -4,一次最多只能提交20000个号码
     -5,非法用户
 */
function send_sms($mobile,$content){
    $sms_username = 'lingli';
    $sms_password = '123456';

    $url = 'http://www.106551.com/ws/Send.aspx?';
    $fields = array(
        'CorpID'=>urlencode($sms_username),
        'Pwd'=>urlencode($sms_password),
        'Mobile'=>urlencode($mobile),
        'Content'=>iconv('UTF-8', 'GBK' , $content),
        'Cell'=>'',
        'SendTime'=>''
    );
     // vde($fields);
    $ret = execPostRequest($url,$fields);
    // vde($ret);
    return trim($ret);

}

//根据参数拼接短信发送URL
function execPostRequest($url,$fields){
	if(empty($url)){ return false;}
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string,'&');
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}


function getminprice( $pay_id ){
	return D('GoodsItem')->where( array('goods_id'=>$pay_id) )->getField('price');
	// vde($price);
	// return
}

function getendtime( $pay_id ){
	// $time= D('Goods')->where( array('goods_id'=>$pay_id)->getField('start_time');
	$start= time();
    $end  = D('Goods')->where( array('goods_id'=>$pay_id) )->getField('end_time');
	$times=($end-$start)/(3600*24);
	if($times<0)$times=0;
	return number_format( ceil( $times ) );
	
}

function getSupportList( $goods_id ){

	$list = M()  ->table( C('DB_PREFIX').'order_info as oi' )
	->field('goods_amount,oi.uid,m.nickname')           //distinct oi.uid,
	->join(  C('DB_PREFIX').'member as m ON oi.uid=m.uid' ,'left')
	// ->join(  C('DB_PREFIX').'member_info as mi ON oi.uid=mi.id' ,'left')
	->where( array('oi.goods_id'=>$goods_id,'oi.pay_status'=>2) )
	//->group('oi.uid')
	->order('oi.order_id desc')
	->select();
	//echo $this->getLastSql();

	return $list;
}	


/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @return string
 */
function friendlyDate($sTime,$type = 'normal') {
	if (!$sTime){return '';}
	//sTime=源时间，cTime=当前时间，dTime=时间差
	$cTime      =   time();
	$dTime      =   $cTime - $sTime;
	$dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
	$dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
	//normal：n秒前，n分钟前，n小时前，日期
	if($type=='normal'){
		if( $dTime < 60 ){
			if($dTime < 10){
				return '刚刚';
			}else{
				return intval(floor($dTime / 10) * 10)."秒前";
			}
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
			//今天的数据.年份相同.日期相同.
		}elseif( $dYear==0 && $dDay == 0  ){
			//return intval($dTime/3600)."小时前";
			return '今天'.date('H:i',$sTime);
		}elseif($dYear==0){
			return date("m月d日 H:i",$sTime);
		}else{
			return date("Y-m-d H:i",$sTime);
		}
	}elseif($type=='mohu'){
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif( $dDay > 0 && $dDay<=7 ){
			return intval($dDay)."天前";
		}elseif( $dDay > 7 &&  $dDay <= 30 ){
			return intval($dDay/7) . '周前';
		}elseif( $dDay > 30 ){
			return intval($dDay/30) . '个月前';
		}
	}elseif($type=='full'){
		return date("Y-m-d , H:i:s",$sTime);
	}elseif($type=='ymd'){
		return date("Y-m-d",$sTime);
	}else{
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif($dYear==0){
			return date("Y-m-d H:i:s",$sTime);
		}else{
			return date("Y-m-d H:i:s",$sTime);
		}
	}
}





/**
     * 将数组转化字符串
     * @param unknown $map
     */
  function joins($map){
		$cope='';
		foreach ($map as $key => $value) {
		    $cope.=implode(",",$value).','; 
		}
		 $map=substr($cope, 0, -1);
		 return $map;
    }
    
    


//==========================================用户相关
//通过wx_openid获取用户ID
function getUidByWxOpenid( $wx_openid ){
	return D('UcenterMember')->where( array('wx_openid'=>$wx_openid) )->field('id');
}
//通过wx_openid获取用户ID
function getUidByMobile( $uid ){
	return D('UcenterMember')->where( array('id'=>$uid) )->getField('mobile');
}

function getUserGroupIdArr( $uid ){
	if( !$uid ){
		$uid = session('user_auth.uid');
	}
	$AuthGroup 	= D('Admin/AuthGroup')->getUserGroup($uid);
	return getIdArr($AuthGroup,'group_id');
}

//用户所属管理组
function isInGroup( $groupName,$uid ){
	if( !$uid ){
		$uid = session('user_auth.uid');
	}
	
	//
	$groupIdArr = C('groupIdArr');
	
	//
	if( in_array($groupIdArr[$groupName],session('user_auth.group_ids')) ){
		return true;
	}
	return false;
}



function jsjump( $url ){
	echo "<script language='javascript' type='text/javascript'>";
	echo "window.location.href='$url'";
	echo '</script>';
}

function getRegion( $region_id ){
	return D('Region')->where( array('region_id'=>$region_id) )->getField('region_name');
}


/**
 * 获取字符串中第一张图片的地址
 * @param unknown $str
 * @return unknown
 */
function getstrpic($str,$add_url=false){
	preg_match ("<img.*src=[\"](.*?)[\"].*?>",$str,$match);
	if($add_url){
		$match[1] = fullImgUrl($match[1]);
	}
	return $match[1];
}
function fullImgUrl($path){
	return 'http://' . $_SERVER['SERVER_NAME'] . $path;
}
function get_cover_path($cover_id){
	$path = get_cover($cover_id,'path');
	return fullImgUrl($path);
}

/*获取时间戳*/
function get_time_prick($time){
	$now=$time?$time:time();
	$y=date("Y",$now);
	$m=date("m",$now);
	$d=date("d",$now);
	$times["start"]=mktime(0,0,0,$m,$d,$y);   //  这里制作一个时间戳 ,对应 2013-10-04 00：00：00
	$times["end"]=mktime(23,59,59,$m,$d,$y);  //  这里对应 2013-10-04 23：59：59

	return $times;
}

/*获取一个月时间戳*/
function get_month_prick($time){
	$now=$time?$time:time();
	$y=date("Y",$now);
	$m=date("m",$now);
	$d=date("d",$now);

	$times["start"]=mktime(0,0,0,$m,1,$y);   //  这里制作一个时间戳 ,对应 2013-10-04 00：00：00
	$times["end"]=mktime(23,59,59,$m,$d,$y);  //  这里对应 2013-10-04 23：59：59

	return $times;
}

/*获取当日后的所有日期-时间戳
*
*
*31-30-29-28....
*
*

*/
function get_months_prick($time){
	$now=$time?$time:time();
	$y=date("Y",$now);
	$m=date("m",$now);
	$d=date("d",$now);
	for ($i=1; $i <=$d ; $i++) {
		if ($i==1) {
			 $r=$d;
			 $j=$d;
		}else{
			$r=($j--)-1;
		}
		$times[$r]["start"]=mktime(0,0,0,$m,$r,$y);   //  这里制作一个时间戳 ,对应 2013-10-04 00：00：00
		$times[$r]["end"]=mktime(23,59,59,$m,$r,$y);  //  这里对应 2013-10-04 23：59：59
	}

	return $times;
}

function get_shop_nume($time){

	$time=get_time_prick($time);
    $where = array(
    	'aim_id' => session('shop_id'),
        'type' => 1,
        'add_time' => array('between',array($time["start"],$time["end"])),
    );
	$num =count(D("FocusCollect")->where($where)->select());
	return $num;
}

function auto_tconfirm_goods( $shop_id ){
	// $time= D('Goods')->where( array('goods_id'=>$pay_id)->getField('start_time');
	$start= time();
   
    $time = D('OrderInfo')->get_shop_time( $shop_id );
    // vde($time);
    foreach ($time as $key => $vo) {
    	// vde($vo['log_time']);
    	$times=($start-$vo['log_time'])/(3600*24);
    	$ceil_times=number_format( ceil( $times ) );
    	if (!empty($vo['order_id'])) {
    		if($ceil_times>5){
    			
    			$map=array(
    				"order_id"=>$vo['order_id'],
    				);
    			$data=array(
    				"shipping_status"=>2,
    				"order_status"=>1,
    				"pay_status"=>2,
    				"order_id"=>$vo['order_id'],
    				"action_user_id"=>-1,
    				"log_time"=>time(),
    				"action_note"=>"已收货"
    				);
    			$datas=array(
    				"shipping_status"=>2,
    			);

    			D('OrderAction')->add($data);
    			
    			D('OrderInfo')->where($map)->save($datas);
    		}
    	}
    	
    	
    	
    }
}


//此函数可以去掉空格，及换行。
function trimall($str)
{
    $qian=array(" ","　","\t","\n","\r");
    $hou=array("","","","","");
    return str_replace($qian,$hou,$str); 
}

function getRegionName( $region_id ){
	return M('Region')->where( array('region_id'=>$region_id) )->getField('region_name');
}




function transformFormArr( $arr ){

	$newArr = array();
	$keyArr = array();

	foreach ($arr as $key=>$val) {
		$keyArr[] = $key;
	}

	$count 		= count( $arr[$keyArr[0]] );
	$keyCount 	= count($keyArr);

	foreach ($arr[$keyArr[0]] as $i=>$val){
		$tempArr = array();
		for( $j=0;$j<$keyCount;$j++ ){
			$key = $keyArr[$j];
			$tempArr[ $key ] = $arr[$key][$i];
		}
		$newArr[] = $tempArr;
	}

	return $newArr;
}
function transformFormArrToJson( $arr ){
	$arr = transformFormArr( $arr );
	return json_encode($arr);
}


function getPerformName($perform_play_name,$start_time){
	if( $perform_play_name=='' ){
		$weekname = C('WEEK_NAME');
		$perform_play_name = date('Y-m-d',$start_time).$weekname[date('w',$start_time)][C('WEEK_TYPE')].date('H:i:s',$start_time);
	}
	return $perform_play_name;
}


function array_add_to2 ($old,$new) {
	if (is_array($old) && is_array($new)) {
		foreach ($new AS $key=>$val) {
			$old[$key] = $val;
		}
	} else {
		return false;
	}
	return $old;
}

/**
 * Api成功提醒（获取数据）
 */
function apiNotice($errmsg,$results=array() ){
	
	G('interface_end');														//接口结束时间
	getImgUrl($imgId);
	$err = array(
			'errcode'			=> 0,
			'interface_runtime'	=> G('interface_begin','interface_end').'s',
			'interface_memory'	=> G('interface_begin','interface_end','m').'kb',
			'response'			=> strtolower( CONTROLLER_NAME.'/'.ACTION_NAME ),
			'results'			=> $results
	);
	return json_encode($err);
}
/**
 * Api操作提醒
 */
function apiDoNotice($ret,$msg='',$expandArr=array() ){
	
	$errcode 	= ($ret)?0:1;
	$errmsg		= ($ret)?'成功':'失败';
	apiError($msg.$errmsg,$errcode,$expandArr);
}

/**
 * Api错误提醒
 * @param unknown $errmsg
 * @param number $errcode
 */
function apiError($errmsg,$errcode=-1,$expandArr=array() ){
	if( $GLOBALS['exitGetApiData'] )return;
	$err = array(
			'errcode'	=> $errcode,
			'errmsg'	=> $errmsg
	);
	$err = array_add_to2($err, $expandArr);
	echo json_encode($err);exitGetApiData();
}
/**
 * Api错误提醒，直接已code形式调用
 * @param unknown $errcode
 */
function apiErrorCode($errcode){	//需要根据调用模块，做返回形式的修改
	echo retApiErrorCode($errcode);exit;
}
function retApiErrorCode($errcode){

	$API_ERR_CODE = C('API_ERR_CODE');
        $err = array(
			'errcode'	=> $errcode,
			'errmsg'	=> $API_ERR_CODE[$errcode]
	);
	return json_encode($err);
}

//合并数组
function array_extend($oldArr,$newArr,$ignoreKey=array()){
	foreach ($newArr as $key=>$val) {
		if( !in_array($key,$ignoreKey) ){
			$oldArr[$key] = $val;
		}
	}
	return $oldArr;
}
//获取图片详细地址
function getImgUrl($imgId,$imgSize=''){
	if( $imgId ){
		return 'http://' . $_SERVER['HTTP_HOST'] . get_cover( $imgId,'path' );
	}
	return '';
}
//批量删除无用数组键
function unsetArr($arr,$fields){
	foreach ($fields as $field) {
		unset( $arr[$field] );
	}
	return $arr;
}

//获取优惠码类型ID
function getSystemCouponTypeInfo( $sign ){
	$systemCouponType = C('SYSTEM_COUPON_TYPE');
	return $systemCouponType[ $sign ];
}

//获取配置项内容描述
function getCName($configFieldName,$idValue,$nameName='name'){
	$configContents = C( $configFieldName );
	return $configContents[$idValue][$nameName];
}
function getCN($idValue,$configFieldName,$nameName='name'){
	return getCName($configFieldName,$idValue,$nameName);
}

/**
 * 
 * @param unknown $dataArr
 * @param string $parent_id_name
 * @param string $relevance_key
 */
function resetGroup( $dataArr,$parent_id_name='parent_id',$relevance_key='' ){
	//将根节点的数据按照parent_subdivision_id划分分组
	$group = array();
	$count = count( $dataArr );
	for( $i=0;$i<$count;$i++ ){
		$parent_id = $dataArr[$i][$parent_id_name];
		if( !isset($group[$parent_id]) ){
			$group[$parent_id] = array();
		}
		if( $relevance_key ){
			$group[$parent_id][ $dataArr[$i][$relevance_key] ] = $dataArr[$i];
		}else{
			$group[$parent_id][] = $dataArr[$i];
		}
	}
	return $group;
}

function generateTree($items,$pid='pid'){
	$tree = array();
	foreach($items as $item){
		if(isset($items[$item[$pid]])){
			$items[$item[$pid]]['child'][] = &$items[$item['id']];
		}else{
			$tree[] = &$items[$item['id']];
		}
	}
	return $tree;
}



function getCjArr($arrList,&$storagerArr,$stCount=1){
	for( $i=0;$i<count($arrList);$i++ ){
		$storagerArr[$stCount][] = $arrList[$i];
		if( isset($arrList[$i]['child']) ){
			getCjArr($arrList[$i]['child'],$storagerArr,$stCount+1);
		}
	}
}
function getItemCount($arr,$pidName='pid'){
	$arrCJ = array();
	getCjArr($arr,$arrCJ);
	
	$countTemp = array();
	$countCJ = count($arrCJ);
	for( $i=$countCJ;$i>0;$i-- ){
		$countCJC = count($arrCJ[$i]);
		for( $j=0;$j<$countCJC;$j++ ){
	
			$id  = $arrCJ[$i][$j]['id'];
			$pid = $arrCJ[$i][$j][$pidName];
	
			if( isset($countTemp[$id]) ){
				$arrCJ[$i][$j]['item_count'] = $countTemp[$id];
			}elseif( !isset($arrCJ[$i][$j]['child']) ){
				$arrCJ[$i][$j]['item_count'] = 1;
			}
			if( !isset($countTemp[$pid]) ){
				$countTemp[$pid] = 0;
			}
			$countTemp[$pid] += $arrCJ[$i][$j]['item_count'];
		}
	}
	
	return $arrCJ;
}
function getFillEmptyArr(&$arrCJ){
	//填充空节点、格式化显示次序
	$pArr = array();
	$emptyArr = array('subdivision_name'=>'','item_count'=>1);
	$emptyPArr = array();
	for($i=1;$i<=count($arrCJ);$i++){
		$pArr[$i] 	= array();

		$tempArr = $arrCJ[$i];
		//将所有节点转换成以父节点为健的数组
		//$tempArr = getIdIndexArr($tempArr);
		
		
		if( $i!=1 ){	//非首节点，节点次序需要已经上层次序重排
			
			$pidTempArr = resetGroup($tempArr,'parent_subdivision_id');
			$arrCJ[$i]  = array();
			
			//将这个数组按照上级$pArr的记载顺序依次展开
			$pIndex = $i-1;
			for($k=0;$k<count($pArr[$pIndex]);$k++){	//根据父级PID的次序来展示当前级别子项显示次序
				$pid = $pArr[$pIndex][$k]['pid'];
				if( isset($pidTempArr[$pid]) ){		//
					for($j=0;$j<count($pidTempArr[$pid]);$j++){
						$arrCJ[$i][] = $pidTempArr[$pid][$j];
							
						//----------
						$has_child = isset( $pidTempArr[$pid][$j]['child'] );
						$currentPid = $pidTempArr[$pid][$j]['id'];
						$pArr[$i][] = array(
								'pid'		=> $currentPid,
								'has_child'	=> $has_child
						);
						
						//echo '$has_child:'.$has_child;
						
						if( !$has_child ){
							
							//echo $currentPid . "<br/>";
							
							$emptyPArr[ $currentPid ] = $pidTempArr[$pid][$j];
							$emptyPArr[ $currentPid ]['subdivision_name'] = '';
						}
						//----------
					}
				}else{
					
					
					//$arrCJ[$i][] = $emptyArr;
					$arrCJ[$i][] = $emptyPArr[$pid];
					//----------
					$pArr[$i][]  = $pArr[$pIndex][$k];
					//----------
				}
			}
		}else{
			
			$pidTempArr = resetGroup($tempArr,'dimension_brand_id');
			$arrCJ[$i]  = array();
			
			foreach ($pidTempArr as $key=>$val){
				for($k=0;$k<count($val);$k++){
					$arrCJ[$i][] = $val[$k];
					
					$has_child = isset($val[$k]['child']);
					$currentPid = $val[$k]['id'];
					
					$pArr[$i][] = array(
							'pid'		=> $val[$k]['id'],
							'has_child'	=> isset($val[$k]['child'])
					);
					
					if( !$has_child ){
							
						//echo $currentPid . "<br/>";
							
						$emptyPArr[ $currentPid ] = $val[$k];
						$emptyPArr[ $currentPid ]['subdivision_name'] = '';
					}
					
				}
			}
		}
	}
	//vde( $emptyPArr );
	//vde( $arrCJ );
	
}
function getDimensionBrand($arrCJ){
	
	//echo 'arrCJ';vd($arrCJ);
	
	$dataSource = $arrCJ[1];
	
	$dimensionArr 		= array('item_count'=>0,'subdivision_name'=>'');
	$dimensionBrandArr 	= array();
	$brandTemp 			= array();
	
	//echo 'dataSource';vd($dataSource);
	
	//获取底层节点的维度和品牌领域
	for($i=0;$i<count($dataSource);$i++){
		//获取维度列表
		if( $dimensionArr['subdivision_name']=='' ){
			$dimensionArr['subdivision_name'] = $dataSource[$i]['dimension_name'];
		}
		$dimensionArr['item_count'] += $dataSource[$i]['item_count'];
			
		//品牌领域
		$dimension_brand_id = $dataSource[$i]['dimension_brand_id'];
		if( !isset($dimensionBrandArr[$dimension_brand_id]) ){
			$dimensionBrandArr[$dimension_brand_id] = array(
					'subdivision_name'	 => $dataSource[$i]['dimension_brand_name'],
					'item_count'		 => 0,
					'dimension_brand_id' => $dataSource[$i]['dimension_brand_id']
			);
		}
		$dimensionBrandArr[$dimension_brand_id]['item_count'] += $dataSource[$i]['item_count'];
	}
	$arrCJPrev = array(array($dimensionArr),$dimensionBrandArr);
	
	for( $i=1;$i<=count($arrCJ);$i++ ){
		$arrCJPrev[] = $arrCJ[$i];
	}
	
	return $arrCJPrev;
}



/**
 * 获取指定日期对应星座
 *
 * @param integer $month 月份 1-12
 * @param integer $day 日期 1-31
 * @return boolean|string
 */
function getConstellation($month, $day)
{
	$day   = intval($day);
	$month = intval($month);
	if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;
	$signs = array(
			array('20'=>'水瓶座'),
			array('19'=>'双鱼座'),
			array('21'=>'白羊座'),
			array('20'=>'金牛座'),
			array('21'=>'双子座'),
			array('22'=>'巨蟹座'),
			array('23'=>'狮子座'),
			array('23'=>'处女座'),
			array('23'=>'天秤座'),
			array('24'=>'天蝎座'),
			array('22'=>'射手座'),
			array('22'=>'摩羯座')
	);
	list($start, $name) = each($signs[$month-1]);
	if ($day < $start)
		list($start, $name) = each($signs[($month-2 < 0) ? 11 : $month-2]);
	return $name;
}
/**
 *  计算.生肖
 *
 * @param int $year 年份
 * @return str
 */
function get_animal($year){
	$animals = array(
			'鼠', '牛', '虎', '兔', '龙', '蛇',
			'马', '羊', '猴', '鸡', '狗', '猪'
	);
	$key = ($year - 1900) % 12;
	return $animals[$key];
}

function getImgHtmlList($imgs,$addClass=""){
	$imgArr = explode(',',$imgs);
	$html = '';
	for( $i=0;$i<count($imgArr);$i++ ){
		$html .= '<img class="'.$addClass.'" alt="" src="'.get_cover($imgArr[$i],'path').'">';
	}
	return $html;
}

//通过类型字符串获取
function getMusicianTypeName( $musician_type_str ){
	$musician_type_arr = F('musician_type_arr');
	if( !$musician_type_arr ){
		$musician_type_arr = D('MusicianType')->select();
		$musician_type_arr = getIdIndexArr($musician_type_arr,'id');
		F('musician_type_arr',$musician_type_arr);
	}
	
	$musician_type_str = trim($musician_type_str,',');
	$musician_type_str_arr = explode(',', $musician_type_str);
	
	$count = count($musician_type_str_arr);
	$temp = '';
	for($i=0;$i<$count;$i++){
		$temp .= $musician_type_arr[ $musician_type_str_arr[$i] ]['musician_type_name'] . ',';
	}
	$temp = rtrim($temp,',');
	
	return $temp;
}

function setListCheckedSign($list,$inArr,$idName='id'){
	foreach ($list as $i=>$val){
		$list[$i]['is_checked'] = 0;
		if( in_array($list[$i][$idName], $inArr) ){
			$list[$i]['is_checked'] = 1;
		}
	}
	return $list;
}

/**
 * 获取当子节点的所有上级节点
 * @param unknown $subdivision_id
 * @param unknown $parentSubdivisionArr
 */
function getParentSubdivisionArr( $subdivision_id,&$parentSubdivisionArr ){
	//获取当前节点的上级节点
	$parent_subdivision_id = D('DimensionBrandSubdivision')->where( array('id'=>$subdivision_id) )->getField('parent_subdivision_id');
	if( $parent_subdivision_id>0 ){		//还存在上级细分，继续追查
		array_unshift($parentSubdivisionArr,$parent_subdivision_id);
		getParentSubdivisionArr( $parent_subdivision_id,$parentSubdivisionArr );
	}
}
/*
function getParentSubdivisionArrAdv( $subdivision_id,&$parentSubdivisionArr ){
	//获取当前节点的上级节点
	$subdivisionInfo = D('DimensionBrandSubdivision')->where( array('id'=>$subdivision_id) )->field('id,parent_subdivision_id,subdivision_name');
	$parent_subdivision_id = $subdivisionInfo['parent_subdivision_id'];
	if( $parent_subdivision_id>0 ){		//还存在上级细分，继续追查
		array_unshift($parentSubdivisionArr,$subdivisionInfo);
		getParentSubdivisionArr( $parent_subdivision_id,$parentSubdivisionArr );
	}
}
*/

//查询条件拼接
function appendSeachRangeStr($_string,$fieldName,$seacField,$is_to_timestamp=0){
	if( I($seacField) ){
		$direction = '';
		if( strpos($seacField,'_start') ){
			$direction = '>=';
		}else if( strpos($seacField,'_end') ){
			$direction = '<=';
		}
		if( $_string!='' ){
			$_string .= ' AND ';
		}
		
		$seacFieldVal = I($seacField);
		if( $is_to_timestamp ){
			$seacFieldVal = strtotime($seacFieldVal);
		}
		$_string .= ' '.$fieldName.' '.$direction." '".$seacFieldVal."'";
	}
	return $_string;
}
function appendQueryStr($_string,$appendStr){
	if( $_string!='' ){
		$_string .= ' AND ';
	}
	$_string .= ' '.$appendStr;
	return $_string;
}






/**
 *
 * @param unknown $type 1 注册 2忘记密码 3设置支付密码 4更改绑定手机
 * @param unknown $mobile
 * @param unknown $minute 设置生存时间
 * @return string  调用成功后将返回一个大于零的整数字符串，表示成功提交的短信数量。例如返回2000，标识从第1条到第2000条都成功提交。
 * 失败返回标识：
 *   -1,用户ID或者密码错误
     -2,缺少必要参数
     -3,含有不合法参数
     -4,一次最多只能提交20000个号码
     -5,非法用户
 */
function sendMobile($mobile,$type,$minute){
    $verify_phone   = $mobile;
    $verify_phone_code = rand(100000,999999);

    session('verify_phone',$verify_phone);

    //设置生命周期
    if(!$minute){
    	session('verify_phone_code',$verify_phone_code);
    	//cookie('verify_phone_code',$verify_phone_code);
    }else{
    	//cookie('verify_phone_code',$verify_phone_code,$minute*60);
    	session('verify_phone_code',array('verify_phone_code'=>$verify_phone_code,'expire'=>(NOW_TIME+$minute*60)));
    }
    
    // session('verify_phone_code',$verify_phone_code,$minute);
    if($type == 1){
    	$content = "您的验证码是".$verify_phone_code;
    }else if($type == 2){
    	$content = "您的验证码是".$verify_phone_code;
    }else if($type == 3){
    	$content = "您的验证码是".$verify_phone_code;
    }else if($type == 4){
    	$content = "您的验证码是".$verify_phone_code;
    }else if($type == 5){
    	$content = "您的验证码是".$verify_phone_code;
    }

    //调试期间返回验证码
    $ret = sendSms($verify_phone,$content,array('template'=>'1','code'=>$verify_phone_code));
    
    
    if($ret==0){
    	$data = array('mobile'=>$mobile,'type'=>$type,'create_time'=>time(),'verify_code'=>$verify_phone_code);
    	if($minute){
    		$data['expire'] = NOW_TIME+$minute*60;
    	}
    	D('VerifyCode')->add($data);
    }
    //$ret = 0;
    return  $ret;
    //成功0、失败1
        
}

/**
 * 发送短信接口
 */
    function sendSms($mobile , $content ,$para=array()){
        $SmsModel = D('Common/Sms');
        
        
        //检测短信轰炸
        if( !$SmsModel->preventSmsBomb( $mobile , C('SMS_CODE_PREVENT_TIME') , C('SMS_CODE_PREVENT_NUM') ) ){
            apiErrorCode( 50012 ); //短信操作过于频繁
        }
        $sms_info = $SmsModel->where(array('status'=>1))->field(true)->find();
        
        //$sms_info['username'] = 'shanjian_yhpc';
        //$sms_info['password'] = 'zzy.123456';
        
        $Sms = D($sms_info['code'],'Sms');
        $data = C('SMS_TPL_REPLACE');
       // vd($sms_info);vd($data);
        $SmsLogModel = D('Common/SmsLog');
        $data['var_code'] = $SmsLogModel->createCode();
        //vde($data);
        if($para&&(strtolower($sms_info['code'])=='ali'))
        {
        	$data = $para;
        }
        $res_data = $Sms->sendSms($mobile , $content ,$data , $sms_info);
        
        //vde($res_data);
        
        $sms_log_id = $SmsLogModel->add($SmsLogModel->create($res_data));
        
        //发送成功
        return $res_data['sms_res'];
        /*if( 0 == $res_data['sms_res']){
            $sms_log_id = $SmsLogModel->add($SmsLogModel->create($res_data));
            if($sms_log_id){
                $this->outputJsonData( $sms_log_id );
            }else{
                apiError('短信发送成功,数据插入失败',50010);
            }
        }else{
            apiError($res_data['sms_res'],50011); //短信平台接口错误
        }*/
    }
    
    
    /**
 * 发送短信接口
 */
    function sendSmsNoReturn($mobile , $content ,$para=array()){
        $SmsModel = D('Common/Sms');
        
        
        //检测短信轰炸
//        if( !$SmsModel->preventSmsBomb( $mobile , C('SMS_CODE_PREVENT_TIME') , C('SMS_CODE_PREVENT_NUM') ) ){
//            apiErrorCode( 50012 ); //短信操作过于频繁
//        }
        $sms_info = $SmsModel->where(array('status'=>1))->field(true)->find();
        
        //$sms_info['username'] = 'shanjian_yhpc';
        //$sms_info['password'] = 'zzy.123456';
        
        $Sms = D($sms_info['code'],'Sms');
        $data = C('SMS_TPL_REPLACE');
       // vd($sms_info);vd($data);
        $SmsLogModel = D('Common/SmsLog');
        $data['var_code'] = $SmsLogModel->createCode();
        //vde($data);
        if($para&&(strtolower($sms_info['code'])=='ali'))
        {
        	$data = $para;
        }
        $res_data = $Sms->sendSms($mobile , $content ,$data , $sms_info);
        
        //vde($res_data);
        
        $sms_log_id = $SmsLogModel->add($SmsLogModel->create($res_data));
        
        //发送成功
        //return $res_data['sms_res'];
        
        
        /*if( 0 == $res_data['sms_res']){
            $sms_log_id = $SmsLogModel->add($SmsLogModel->create($res_data));
            if($sms_log_id){
                $this->outputJsonData( $sms_log_id );
            }else{
                apiError('短信发送成功,数据插入失败',50010);
            }
        }else{
            apiError($res_data['sms_res'],50011); //短信平台接口错误
        }*/
    }

    /**
     * 发送短信接口，不需要记录
     */
        function sendSmsNo($mobile , $content ){
            $SmsModel = D('Common/Sms');
            //检测短信轰炸
            if( !$SmsModel->preventSmsBomb( $mobile , C('SMS_CODE_PREVENT_TIME') , C('SMS_CODE_PREVENT_NUM') ) ){
                apiErrorCode( 50012 ); //短信操作过于频繁
            }
            $sms_info = $SmsModel->where(array('status'=>1))->field(true)->find();
            $Sms = D($sms_info['code'],'Sms');
            $data = C('SMS_TPL_REPLACE');
            $res_data = $Sms->sendSms($mobile , $content ,$data , $sms_info);

            //发送成功
            return $res_data['sms_res'];
        }

 /**
  * 生成随机字符串
  * @param int       $length  要生成的随机字符串长度
  * @param string    $type    随机码类型：0，数字+大小写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
  */
 function randCode($length = 5, $type = 0) {
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } elseif ($type == "-1") {
        $string = implode("", $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $string[rand(0, $count)];
    }
    return $code;
 }
/**
 *验证手机验证码
 * @param unknown
 * @param unknown $mobile
 * @param unknown $verify
 */
function checkMobileCode($mobile,$verify){
    /*$verify_phone       = $mobile;
    $verify_phone_code  = $verify;
    $verify_session = session('verify_phone_code');
    if( $verify_phone==session('verify_phone') ){
        if( $verify_phone_code==$verify_session ){
            return 1;
        }else{

        	if($verify_session && $verify_phone_code==$verify_session['verify_phone_code'] && NOW_TIME<=$verify_session['expire']){
        		return 1;
        	}else{
        		return 0;
        	}
            
        }
    }else{
        return 0;
    }*/
    $search = array('mobile'=>$mobile,'verify_code'=>$verify);
    $VerifyCode = D('VerifyCode');
    $info = $VerifyCode->where($search)->order('id desc')->find();
    if($info&&$info['expire']==0){
    	return 1;
    }else if($info&&NOW_TIME<=$info['expire']){
    	return 1;
    }else{
    	return 0;
    }
}





function get_coverfile($cover_id, $field = null){
    if(empty($cover_id)){
        return false;
    }
    $picture = M('File')->where(array('status'=>1))->getById($cover_id);
    //vde($picture);
    return empty($field) ? $picture : $picture[$field];
}

function get_file_path($cover_id){
    if(empty($cover_id)){
        return false;
    }
    $picture = M('File')->where('id='.$cover_id)->find();
    return 'http://' . $_SERVER['HTTP_HOST'] . '/Uploads/Download/'.$picture['savepath'].$picture['savename'];
}


function shuffleRankingScoreRecord(){
	$ranking=M('Ranking')->order('create_time desc')->find();
	$ret=M('RankingScoreRecord')->where('ranking_id='.$ranking['id'])->setField('rounds','0');
	$list=M('RankingScoreRecord')->where('ranking_id='.$ranking['id'])->order('ranking_num desc')->select();
	if ($ret) {
	    // shuffle($list);
	    for ($i=0; $i <500 ; $i++) { 
	      $data=array(
	      	'rounds'=>1,
	      	);
	       M('RankingScoreRecord')->where('id='.$list[$i]['id'])->setField($data); 
	    }
	}
	mergeStrs();
}

//合并字符串
function mergeStrs(){
		$p=0;
		C('LIST_ROWS',500);
       $page_size=500;
       $mapes=array(

           'page_size'=>$page_size,

           );
       //获取艺人列表
       $zuiList=  getApiData('Musician/getMusicianRankList',$mapes);
       $list=$zuiList['dataset'];
       // vde($list);
       for ($i=0; $i <500 ; $i++) { 
         $data=array(

         	'ranking_nums'=>$i+1,
         	);
         // vd($list[$i]);
          M('RankingScoreRecord')->where('id='.$list[$i]['cid'])->setField($data); 
       }
}

//合并字符串
function mergeStr($str1,$str2){
	return $str1.$str2;
}


//获取Api接口数据
function getApiDataSimple($controllerAction,$parameter){

    $parameter['request_time'] = time();
    $parameter['appid'] = '10012';
    $parameter['appkey'] = 'NGQ2MTFmNzI2ZTY4MmVlMw==';
    $parameter['access_token'] = md5( $parameter['appid'] . $parameter['request_time'] . $parameter['appkey'] );
    $url = 'http://'.$_SERVER['HTTP_HOST'].'/'.U('Api/'.$controllerAction,$parameter);
    $dataJson = file_get_contents($url);
    $dataArr = json_decode($dataJson,true);

    return $dataArr;
}
//获取Api接口数据
function getApiData($controllerAction,$parameter,$onlyRet=0){
        
	$defaultOpt = array();
	if( I('page_size')*1 ){
		$defaultOpt['page_size'] = I('page_size');
	}else{
		$defaultOpt['page_size'] = C('LIST_ROWS');
	}
	if( I('p')*1 )$defaultOpt['p'] = I('p');

	$parameter = array_add_to2($defaultOpt, $parameter);
	$parameter['request_time'] = time();
	$parameter['appid'] = '10001';
	$parameter['appkey'] = 'Y9FIGZYSYY';
	$parameter['access_token'] = md5( $parameter['appid'] . $parameter['request_time'] . $parameter['appkey'] );
        
        
        
	//$controllerActionArr = explode('/',$controllerAction);

	/*ob_start();
	 $GLOBALS['getData'] = true;
	 call_user_func_array(array(A('Api/'.$controllerActionArr[0]),$controllerActionArr[1]), $parameter );
	 $dataJson = ob_get_contents();
	ob_end_clean();*/

	/*
	 //请求参数 格式a/2/b/3
	 $queryArr = array();
	 $keyNum = 0;
	 foreach ( $parameter as $key=>$value ){
		$queryArr[$keyNum++] = $key .'/'. $value;
		}
		$queryStr = implode('/',$queryArr);
	*/

	//请求URL
	//$url = 'http://'.$_SERVER['HTTP_HOST'].'/Api/'.$controllerActionArr[0].'/'.$controllerActionArr[1].'/'.$queryStr;
        
	$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.U('Api/'.$controllerAction,$parameter);
//        vde($url);
	$dataJson = file_get_contents($url);
        
	//vd($parameter);echo $url;vde($dataJson);
        //vde($dataJson);
        
	$dataArr = json_decode($dataJson,true);
        

	if($dataArr['errcode']>0 || $dataArr['errcode']==-1){
		if( $onlyRet )return array();
		return $dataArr;
	}else{
		return $dataArr['results'];
	}


}
function exitGetApiData(){
	if( !$GLOBALS['getData'] ){
		exit;
	}else{
		$GLOBALS['getData'] 		= false;
		$GLOBALS['exitGetApiData'] 	= true;
	}
}

//是否高级帐号
function isAdvAccount($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    if( is_administrator($uid) || in_array($uid, array(8,9)) ){
		return true;
	}
	return false;
}
function getKeyArr( $arr ){
	$keyArr = array();
	foreach ($arr as $key=>$val){
		$keyArr[] = $key;
	}
	return $keyArr;
}

//获取记录类型加分规则
function getRecordAddScoreRule(){
	$DATA_RECORD_GROUP_RANKING_RULE = C('DATA_RECORD_RANKING_RULE');
	return $DATA_RECORD_GROUP_RANKING_RULE;
}




function get_filename($file_id){
        $file_name=D('File')->where('id='.$file_id)->getField('name');
        return $file_name;
    }






function get_mobile_any(){
    return '137'.rand(1,9).rand(1,9).substr(time(),4);
}


/**
 * 验证链接权限
 */
function auth_link($url = 'Dealer/Index/index', $type = array('in','1,2'))
{
    $CombaseController = A('Common/Combase');
    return $CombaseController->checkRule($url, $type);
}





