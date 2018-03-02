<?php
namespace Common\Payment;

/**
 * 余额付款
 */
class BalancePayment extends BasePayment{
		
	//初始化
	public function _initialize(){
		//$this->order_sn_name = 'out_trade_no';	//订单号名称
	}
		
	//支付方式的基本信息
	protected  function baseInfo(){
		return array(
				'pay_name'		=>	'余额付款',					
				'pay_code'		=>	'balance',					
				'pay_desc'		=>	'用户账户余额需要大于订单金额方可进行',
				'is_cod'		=>	0,							
				'is_online'		=>	1,							
				'author'		=> 	'闪尖科技',					
				'version'		=>	'1.0.0',					
				'paymentwebsite'=>	'http://www.51shanjian.com'		
		);
	}
	//配置信息
	public function config(){
		return array();
	}
	
	
	
	//
	public function getPayCode( $order ){
		
		$uid 			= $order['uid'];
		$order_amount 	= $order['order_amount'];
		$AccountLog 	= D('AccountLog');
			
		//判断用户当前余额是否足够支付全款
		if( !$AccountLog->checkUserMoneyIsEgt($uid,$order_amount) ){	//
			$user_money = $AccountLog->getUserMoney($uid);
			return array(
					'errcode'		=> 40040,
					'errmsg'		=> '订单总额'.$order_amount.'元，您的当前余额'.$user_money.'元，请充值。',
					'order_amount'	=> $order_amount,
					'user_money'	=> $user_money
			);
		}
		//vde($order);
		
		//足够的话，创建订单支付记录
		//更具不同订单类型进行用户 
		$balancePayRet = 0;
                switch ( $order['order_suffix'] ){
			case "og":
				$order_group_id = $order['id'];
				$balancePayRet = $AccountLog->createPayOrderGroupByBalanceLog($uid,$order_amount,$order_group_id);
				break;
                        case "oi":
                                $order_id = $order['order_id'];
				$balancePayRet = $AccountLog->createPayOrderByBalanceLog($uid,$order_amount,$order_id);
				break;
                        case "ot":
                                $order_id = $order['order_id'];
				$balancePayRet = $AccountLog->createPayOrderByBalanceLogWithObj($uid,$order_amount,$order['order_id'],3);
				break;
                        case "oa":
                                $order_id = $order['order_id'];
				$balancePayRet = $AccountLog->createPayOrderByBalanceLogWithObj($uid,$order_amount,$order['order_id'],5);
				break;
                        case "ol":
                                $order_id = $order['order_id'];
				$balancePayRet = $AccountLog->createPayOrderByBalanceLogWithObj($uid,$order_amount,$order['order_id'],6);
				break;
			default:
				$balancePayRet = $AccountLog->createPayBalanceLog(array(
					'uid'			=> $uid,
					'user_money'	=> -$order_amount,
					'change_desc'	=> '余额支付',
					'change_type'	=> 97,
					'extend'		=> $order['order_sn']
				));
				break;
		}
		
		//vde($balancePayRet);	
		//支付成功后调用订单支付成功后的回调方法，改变各表与记录的数据
		if( !$balancePayRet ){		//余额支付失败
			//余额支付未完成，订单状态尚未改变
			return array(
					'errcode'		=> 40106,
					'errmsg'		=> '支付失败'
			);
		}else{
			//支付成功回调处理
			$ret = D('Order')->updateOrderPaySuccessStatus( $order['order_sn'] );			
		}
		
		return array(	//支付成功
				'errcode'		=> 0,
				'errmsg'		=> '支付成功'
		);	
	}
	
	//执行余额付款
	public function local_pay( $order ){
		//检测用户余额是否足以支付
		$user_money = D('AccountLog')->get_user_money( $order['uid'] );
		
		//不足，提示，并终止后续执行
		if( $user_money<$order['order_amount'] ){
			$ret['noticeInfo'] = array(
					'status'	=> '0',				//付款失败
					'contents'	=> '订单提交成功，余额不足，请充值或使用其他方式付款。',
			);
			return $ret;
		}
		//echo $user_money;vde( $order );
		
		//足以，直接从用户账户余额中减去订单总额（添加余额使用日志）
		
		$_SESSION['user_pay_payment'] = 1;
		//修改订单付款状态等信息
		$order['pay_status'] = 2;
		$order['action_note'] = '订单：'.$order['order_sn'].',使用余额付款';
		D('OrderInfo')->updateOrderInfo( $order );
		
		D('AccountLog')->get_user_money( $order['uid'],true );	//重置用户表的订单余额
		
		//提示用户付款成功
		$ret['noticeInfo'] = array(
				'status'	=> '1',				//成功
				'contents'	=> '付款成功！',
		);
		return $ret;
	}
	
	//
	public function callback(){
			
	}
	
	
	
	
	
	
	
	
	
	/*
	public function getPayCode( $order ){
	
	
		//检测用户余额是否足以支付
		$user_money = D('AccountLog')->get_user_money( $order['uid'] );
	
		//不足，提示，并终止后续执行
		if( $user_money<$order['order_amount'] ){
			$ret['noticeInfo'] = array(
					'status'	=> '0',				//付款失败
					'title'		=> '订单提交成功',
					'sub_title'	=> '订单提交成功，余额不足，请及时付款。',
					'contents'	=> '您的订单已成功提交！<a href="'.U('Account/orderlist').'">查看订单详情</a>',
			);
			return $ret;
		}
		//echo $user_money;vde( $order );
	
		//足以，直接从用户账户余额中减去订单总额（添加余额使用日志）
	
	
		//修改订单付款状态等信息
		$order['pay_status'] = 2;
		$order['action_note'] = '订单：'.$order['order_sn'].',使用余额付款';
		D('OrderInfo')->updateOrderInfo( $order );
		D('AccountLog')->get_user_money( $order['uid'],true );	//充值用户表的订单余额
	
		//提示用户付款成功
		$ret['noticeInfo'] = array(
				'status'	=> '1',				//成功
				'title'		=> '订单提交成功',
				'sub_title'	=> '订单提交成功',
				'contents'	=> '您的订单已成功提交！<a href="'.U('Account/orderlist').'">查看订单详情</a>',
		);
		return $ret;
	}
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
