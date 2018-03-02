<?php
namespace Common\Payment;

/**
 * 依据支付宝最新接口改写而成
 * @author wei
 * @date 2015年6月25日 17:11:32
 */
class AlipayPayment extends BasePayment{
    Protected $autoCheckFields = false;
	//初始化
	public function _initialize(){
		$this->order_sn_name = 'out_trade_no';	//订单号名称
	}
	
	//------------------------------
	//返回订单号名称
	public function getOrderSn( $getValue=false ){
		if( $getValue ){
			return $this->getOrderSnValue();
		}else{
			return $this->order_sn_name;
		}
		
	}
	
	//支付方式的基本信息
	protected  function baseInfo(){
		return array(
				'pay_name'		=>	'支付宝',					//支付工具名称
				'pay_code'		=>	'alipay',					//支付英文标识
				'pay_desc'		=>	'支付宝网站(www.alipay.com) 是国内先进的网上支付平台。支付宝收款接口：在线即可开通，零预付，免年费，单笔阶梯费率，无流量限制。立即在线申请',					//支付描述
				'is_cod'		=>	0,							//货到付款
				'is_online'		=>	1,							//在线支付
				'author'		=> 	'闪尖科技',					//插件作者
				'version'		=>	'1.0.0',					//插件版本
				'paymentwebsite'=>	'http://www.alipay.com'		//支付方式网址
		);
	}
	
	//需要填充的配置
	public function config(){
		//农行，兴业，工行，交行，建行，中国银行，招行，浦发，  		邮政储蓄
		$BACK = array(
					'BOCB2C'	=> '中国银行 ',
					'ABC'		=> '农业银行',
					'ICBCB2C'	=> '中国工商银行 ',
					'CMB'		=> '招商银行',
					'CCB'		=> '中国建设银行',
					'SPDB'		=> '上海浦东发展银行',
					'CIB'		=> '兴业银行',
					'POSTGC'	=> '中国邮政储蓄银行',
					'COMM-DEBIT'=> '交通银行'
				);
		
		
		return array(
			array(
					'name' 		=> 'alipay_account',
					'title'		=> '支付宝帐户', 
					'remark'	=> '必填',
					'is_show'   => '1',       
					'type' 		=> 'text',   
					'value' 	=> ''
				),
			array(
					'name' 		=> 'alipay_partner',
					'title'		=> '合作者身份ID',
					'remark'	=> '必填，合作身份者id，以2088开头的16位纯数字',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
			),
			array(
					'name' 		=> 'alipay_appid',
					'title'		=> 'appid',
					'remark'	=> 'appid',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
			),
			/*
			array(
					'name' 		=> 'alipay_rsa_private_key_pkcs8',
					'title'		=> 'rsa_private_key_pkcs8',
					'remark'	=> 'rsa_private_key_pkcs8',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
			),
			*/
			array(
					'name' 		=> 'alipay_key',
					'title'		=> '交易安全校验码',
					'remark'	=> '必填，安全检验码，以数字和字母组成的32位字符',
					'is_show'   => '1',
					'type' 		=> 'text',
					'value' 	=> ''
				),
			array(
					'name' 		=> 'web_support_back',
					'title'		=> 'PC端支持银行',
					'remark'	=> '关联网银支付银行',
					'is_show'   => '1',
					'type' 		=> 'checkbox',
					'value' 	=> '',
					'extra'		=> $BACK
				),
			array(
					'name' 		=> 'mobile_support_back',
					'title'		=> 'Mobile端支持银行',
					'remark'	=> '关联网银支付银行',
					'is_show'   => '1',
					'type' 		=> 'checkbox',
					'value' 	=> '',
					'extra'		=> $BACK
				),
		);
	}

	/**
	 * 生成支付代码
	 * @param array $order	订单信息数组
	 * 			主要元素
	 * 				order_sn		订单号
	 * 				subject			商品名称
	 * 				order_amount	订单总额
	 * 				body			订单描述
	 * 				show_url
	 * @return multitype:string
	 */
	public function getPayCode( $order ){
		
		
		//vde($order);
		
		if( isset($order['bank']) && !empty($order['bank']) ){		//网银支付
			$this->getPayCode_Bank( $order );
		}else if( isMobile() ){										//手机处理
			$this->getPayCode_Mobile( $order );			
		}else{
			
			//获取当前支付方式的配置信息
			$config = $this->get_config();
			$alipay_config = $this->get_alipay_config( $config );
			
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" 			=> "create_direct_pay_by_user",
					"partner" 			=> trim($config['alipay_partner']),
					"payment_type"		=> 1,
					"notify_url"		=> C('NOTIFY_URL'),		//服务器异步通知页面路径,支付宝服务器主动通知商户网站里指定的页面http路径。
					"return_url"		=> C('RETURN_URL'),		//页面跳转同步通知页面路径,支付宝处理完请求后，当前页面自动跳转到商户网站里指定页面的http路径。
					"seller_email"		=> trim($config['alipay_account']),
					"out_trade_no"		=> $order['order_sn'],
					"subject"			=> ($order['subject'])?$order['subject']:$order['body'],			//商品的标题/交易标题/订单标题/订单关键字等。该参数最长为128个汉字。
					"total_fee"			=> $order['order_amount'],		//该笔订单的资金总额，单位为RMB-Yuan。取值范围为[0.01，100000000.00]，精确到小数点后两位。
					"body"				=> $order['body'],				//对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
					"show_url"			=> $order['show_url'],			//商品展示网址。收银台页面上，商品展示的超链接。
					"anti_phishing_key"	=> trim($config['alipay_key']),
					"exter_invoke_ip"	=> GetIP(),
					"_input_charset"	=> 'utf-8'
			);
			
			//vde($parameter);
			
			//建立请求
			require_once(C('PAYMENT_PATH')."/lib/alipay/AlipaySubmit.class.php");
			$alipaySubmit = new \Lib\alipay\AlipaySubmit($alipay_config);
			
			return $alipaySubmit->buildRequestForm($parameter);
		}
	}
	
	
	
	
	//手机端支付
	public function getPayCode_Mobile( $order ){
		//获取当前支付方式的配置信息
		$config = $this->get_config();
		$alipay_config = $this->get_alipay_mobile_config( $config );
						
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" 		=> "alipay.wap.create.direct.pay.by.user",
				"partner" 		=> trim($alipay_config['partner']),
				"seller_id" 	=> trim($alipay_config['partner']),
				"payment_type"	=> '1',
				"notify_url"	=> C('NOTIFY_URL'),
				"return_url"	=> C('RETURN_URL'),
				"out_trade_no"	=> $order['order_sn'],
				"subject"		=> ($order['subject'])?$order['subject']:$order['body'],
				"total_fee"		=> $order['order_amount'],
				"show_url"		=> '',
				"body"			=> ($order['subject'])?$order['subject']:$order['body'],
				"it_b_pay"		=> '',
				"extern_token"	=> '',
				//"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
				"_input_charset"	=> trim(strtolower('utf-8'))
		);
				
		//vd( $alipay_config );vde( $parameter );
		
		require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayMobileSubmit.class.php");
		//建立请求
		$alipaySubmit = new \Lib\alipay\AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;		
	}
		
	//网银支付
	public function getPayCode_Bank( $order ){
		
		//获取当前支付方式的配置信息
		$config = $this->get_config();
		$alipay_config = $this->get_alipay_bank_config( $config );
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" 			=> "create_direct_pay_by_user",
				"partner" 			=> trim($alipay_config['partner']),
				"seller_email" 		=> trim($alipay_config['seller_email']),
				"payment_type"		=> "1",
				"notify_url"		=> C('NOTIFY_URL'),
				"return_url"		=> C('RETURN_URL'),
				"out_trade_no"		=> $order['order_sn'],
				"subject"			=> $order['subject'],
				"total_fee"			=> $order['order_amount'],
				"body"				=> '',
				"paymethod"			=> "bankPay",
				"defaultbank"		=> $order['bank'],
				"show_url"			=> '',
				"anti_phishing_key"	=> '',	//防钓鱼时间戳
				"exter_invoke_ip"	=> '',	//客户端的IP地址
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayBankSubmit.class.php");
		
		//建立请求
		$alipaySubmit = new \Lib\alipay\AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
		exit;
	}
	
	
	
	
	
	
	
	/**
	 * 支付通知处理
	 * @param bool $Notify 检测类型 	默认服务器通知，false则为页面跳转
	 */
	public function callback( $Notify=true ){
		
		$config = $this->get_config();

		//echo "config:";vd($config);
		
		
		if( isMobile()  || $_POST['sign_type']=='RSA'  ){	//手机端回调
			require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayMobileNotify.class.php");
			$alipay_config = $this->get_alipay_mobile_config( $config );
			
			//echo 'alipay_config:';vd($alipay_config);
			//echo "isMobile:";
		}else{				//PC端回调
			require_once(C('PAYMENT_PATH')."/lib/alipay/AlipayNotify.class.php");
			$alipay_config = $this->get_alipay_config( $config );
			//vd($alipay_config);
		}
		
		//
		$alipayNotify = new \Lib\alipay\AlipayNotify($alipay_config);
				
		//验证是否为支付宝发送过来的请求
		if( $Notify ){		//服务器通知
			//sf(1,"C:\\paylog\\post_Notify.php");
			///sf($_POST,"C:\paylog\ehiwi\post_Notify.php");
			$verify_result = $alipayNotify->verifyNotify();			//	$_POST
			//vd($verify_result);
		}else{		
			//sf(1,"C:\\paylog\\get_callback.php");
			///sf($_POST,"C:\paylog\ehiwi\get_callback.php");
			//页面跳转
			$verify_result = $alipayNotify->verifyReturn();			//	$_GET
			//vd($verify_result);
			//vd($_GET);echo "============".$verify_result."-----------------";exit;
		}
		//
		
		if( $verify_result ){
			
			if( $Notify ){	//服务器通知，获取支付状态
				$trade_status = $_POST['trade_status'];
			}else{	//页面跳转通知
				$trade_status = $_GET['result'];	//
			}
			//ee($trade_status);
			//vd($_GET);
			
			$successArr = array('TRADE_FINISHED','TRADE_SUCCESS','success');
			
			if( in_array( $_GET['trade_status'],$successArr ) || in_array($trade_status,$successArr ) ){
				return true;
			}
		}
		return false;
	}

	
	
	
	//支付方式扩展
	//=====================================================================================================================
	//PC端
	public function get_alipay_config( $config ){
		$alipay_config['partner']		= $config['alipay_partner'];//合作身份者id，以2088开头的16位纯数字
		$alipay_config['key']			= $config['alipay_key'];	//安全检验码，以数字和字母组成的32位字符
		$alipay_config['sign_type']    	= strtoupper('MD5');
		$alipay_config['input_charset']	= strtolower('utf-8');
		$alipay_config['cacert']    	= C('PAYMENT_PATH').'/lib/alipay/cacert.pem';	//ca证书路径地址，用于curl中ssl校验//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['transport']    	= 'http';					//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http

		//vde( $alipay_config );
		
		return $alipay_config;
	}
	
	//移动端
	public function get_alipay_mobile_config( $config ){
		
		$alipay_config['partner']		= $config['alipay_partner'];
		//收款支付宝账号
		$alipay_config['seller_id']		= $config['alipay_account'];
		//签名方式 不需修改
		$alipay_config['sign_type']    	= strtoupper('RSA');
		
		//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config['input_charset']	= strtolower('utf-8');
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    	= C('PAYMENT_PATH').'/lib/alipay/cacert.pem';
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config['transport']    	= 'http';
		
		$alipay_config['private_key_path']		= C('PAYMENT_PATH').'/lib/alipay/key/rsa_private_key.pem';	//商户的私钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
		$alipay_config['ali_public_key_path']	= C('PAYMENT_PATH').'/lib/alipay/key/alipay_public_key.pem';//支付宝公钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
		
		
		
		return $alipay_config;
	}
	
	//移动端
	public function get_alipay_app_mobile_config( $oInfo ){
                
		$baseConfig = $this->get_config();
		$config['private_key_path']	= C('PAYMENT_PATH').'/lib/alipay/key/rsa_private_key.pem';	//商户的私钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
		$config['sign_type']    	= strtoupper('RSA');

		$alipay_config['service']			= "mobile.securitypay.pay";
		$alipay_config['partner']			= $baseConfig['alipay_partner'];
		$alipay_config['_input_charset']	= strtolower('utf-8');
		$alipay_config['notify_url']    	= C('NOTIFY_URL');//urlencode();
		$alipay_config['out_trade_no']    	= $oInfo['order_sn'];
		$alipay_config['payment_type']    	= 1;
		$alipay_config['seller_id']			= $baseConfig['alipay_account'];
		$alipay_config['subject']    		= $oInfo['title'];
		$alipay_config['total_fee']    		= $oInfo['order_amount'];

		$alipay_config['body']    			= $oInfo['body'];
		$alipay_config['it_b_pay']    		= '30m';
	
		$LIB_PATH = C('PAYMENT_PATH').'/lib/alipay';
		require_once($LIB_PATH.'/AlipayMobileSubmit.class.php');
		$notify = new \lib\alipay\AlipaySubmit($config);
                
		$alipay = $notify->buildRequestPara($alipay_config);
                
		
		$alipay_config['notify_url']   = $alipay['notify_url'] 	= C('NOTIFY_URL');
		$alipay['order_info_str'] = $notify->createSortPara($alipay_config);
		
		$alipay['app_id']		= $baseConfig['alipay_appid'];
		
		
		//签名方式 不需修改
		//$alipay['body']    	= $oInfo['body'];
		//$alipay['it_b_pay']    	= C('ALIPAY_IT_B_PAY');
		//字符编码格式 目前支持 gbk 或 utf-8
	
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		//        $alipay_config['cacert']    	= C('PAYMENT_PATH').'/lib/alipay/cacert.pem';
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay['transport']    	= 'http';
		

		/*$alipay_config['ali_public_key_path']	= C('PAYMENT_PATH').'/lib/alipay/key/alipay_public_key.pem';*///支付宝公钥（后缀是.pen）文件相对路径	如果签名方式设置为“0001”时，请设置该参数
	
	
	
		return $alipay;
	}
	
	//网银支付
	public function get_alipay_bank_config( $config ){
		
		$alipay_config['partner']		= $config['alipay_partner'];;
		
		//收款支付宝账号
		$alipay_config['seller_email']	= $config['alipay_account'];;
		
		//安全检验码，以数字和字母组成的32位字符
		$alipay_config['key']			= $config['alipay_key'];
		
		//签名方式 不需修改
		$alipay_config['sign_type']    = strtoupper('MD5');
		
		//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config['input_charset']= strtolower('utf-8');
		
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = C('PAYMENT_PATH').'/lib/alipay/cacert.pem';
		
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config['transport']    = 'http';
		
		
		return $alipay_config;
	}
	
	
	
	
	
}
