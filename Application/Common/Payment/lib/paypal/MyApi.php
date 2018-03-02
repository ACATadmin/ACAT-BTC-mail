<?php
class MyApi
{
	var $parameters;//请求参数，类型为关联数组
	//var $action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';				//测试?locale.x=utf
	var $action = 'https://www.paypal.com/cgi-bin/webscr';					//正式
	
	
	/**
	 * 	作用：设置请求参数
	 */
	public function setParameter($parameter, $parameterValue){
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	public function create_form(){		
		//测试
		//$this->parameters['business'] = 'ehiwi_bus@qq.com';

		$sHtml = "<meta http-equiv='content-type' content='text/html' charset='utf-8'>";
		$sHtml .= '<form style="display:none;" id="paypalsubmit" action="'.$this->action.'" method="post">'.
				 '<input name="cmd" 			value="_xclick">'.
				 '<input name="business" 		value="'.$this->parameters['business'].'">'.
				 '<input name="item_name" 		value="'.$this->parameters['item_name'].'">'.
				 '<input name="item_number" 	value="'.$this->parameters['item_number'].'">'.
				 '<input name="invoice" 		value="'.$this->parameters['invoice'].'">'.
				 '<input name="currency_code" 	value="USD">'.
				 '<input name="amount" 			value="'.$this->parameters['amount'].'">'.
				 '<input name="notify_url" 		value="'.$this->parameters['notify_url'].'" />'.
			 	 '<input name="cancel_return" 	value="'.$this->parameters['cancel_return'].'" />'.
				 '<input name="return" 			value="'.$this->parameters['return'].'" />'.
				 '<input type="submit" 			value="PayPal">'.
				 '</form>';
		
		$sHtml = $sHtml."<script>document.forms['paypalsubmit'].submit();</script>";
		return $sHtml;
	}
	
	
	public function notify_validate(){
		$_POST['cmd'] = '_notify-validate';
		
		$ret = vpost($this->action,$_POST);
		if( $ret == 'VERIFIED' ){
			return true;
		}
		return false;
	}
	
	public function vpost($url,$data,$cookie){ // 模拟提交数据函数
	
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		curl_setopt($curl, CURLOPT_REFERER,'https://www.baidu.com');// 设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
			echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}
	
	
	
	
	
	public function trimString($value)
	{
		$ret = null;
		if (null != $value)
		{
			$ret = $value;
			if (strlen($ret) == 0)
			{
				$ret = null;
			}
		}
		return $ret;
	}
	
	
}