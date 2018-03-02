<?php
namespace lib\wxpay;

class  SDKRuntimeException extends \Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>