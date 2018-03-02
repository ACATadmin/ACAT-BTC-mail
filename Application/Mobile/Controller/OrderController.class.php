<?php
namespace Mobile\Controller;
/**
 * 前台演出类型控制器
 * 
 */
class OrderController extends MobileController {
	
	//支付页
	public function paying($type=''){

        $this->mInfo =getApiData('User/getUserInfo',array('uid'=>is_login()) );
		if( $type == '1' ){
            $this->title='充值';
			$this->BalanceInfo = getApiData('Account/getBalanceInfo',array('uid'=>is_login()));
			$this->display('Account/recharge');
            exit;
		}
		
		
		$wx_openid = session('wx_openid');
		//echo '$wx_openid:'.$wx_openid;exit;
		
		$this->display('pay');
	}
	
	
	//获取支付码（创建支付请求）
    public function getPayCode( $pay_code ){
		
    	$order_id 		= I('order_id');
    	$order_suffix 	= I('order_suffix');	//order_info=>oi	order_group=>og
    	$oInfo = D('Order')->getOrderInfo(array(
    			'order_val'			=> $order_id,
    			'order_suffix'		=> $order_suffix,
    			'pay_code'			=> $pay_code,
    	));
    	$oInfo['order_suffix'] = $order_suffix;
        if(I('successJumpUrl')){
            $oInfo['successJumpUrl'] = I('successJumpUrl'); //成功后页面跳转地址（先用于微信）
        }
    	//$oInfo['successJumpUrl'] = I('successJumpUrl');	//成功后页面跳转地址（先用于微信）
    	//retApiErrorCode
    	
        if($oInfo['order_suffix']=='oa'){
            $info = D('ActivityOrderinfo')->checkLimit($oInfo['uid'],$oInfo['activity_id'],$oInfo['item_id'],$oInfo['buy_num'],2);
            if($info>0){
                $disInfo = retApiErrorCode($info,false);    //提示支付成功
                $this->ajaxReturn($disInfo,'JSON');
            }
        }
        if($pay_code=='balance'){
            $ret = D('Member')->verifyPayPassword($oInfo['uid'],I('password'));
            if(!$ret){
                $disInfo = retApiErrorCode(40039,false);    //提示支付密码错误
                $this->ajaxReturn($disInfo,'JSON');
            }
        }
    	if( $oInfo['is_pay_success'] == 1 ){
    		$disInfo = retApiErrorCode(40107,false);	//提示支付成功
    		
    	}else{		//echo "----------";
    		$disInfo = D('Payment')->do_pay( $oInfo,$pay_code );
    	}
    	if( is_array($disInfo) ){
    		$disInfo['successJumpUrl'] = ($oInfo['successJumpUrl'])?$oInfo['successJumpUrl']:'';
    		//$disInfo = json_encode($disInfo);
    		$this->ajaxReturn($disInfo,'JSON');
    	}
    	
    	//vde($disInfo);
    	echo $disInfo;
    }
    
    
    //================================================================================================================
    
   
    
    //================================================================================================================
    //执行回调处理
    private function do_callback( $Notify ){
    	D('Qa')->add(array('q'=>1,'a'=>2)); 
    	//echo '==================================';
    	
    	//获取订单号，需要兼容不同的支付工具的返回的订单号的键
    	$order_width_suffix = getOrderSn();
        

    	//通过订单号获取订单详情【判断不同的订单类型（票组订单，票订单，众筹活动订单，充值订单）】
    	$oInfo = D('Order')->getOrderInfoBySuffixSn( $order_width_suffix );
    	//vde($oInfo);
    	sf($oInfo,"C:\\paylog\\data_oInfo.php");
    	//$xml = $GLOBALS['HTTP_RAW_POST_DATA'];vd($xml);
    	
    
    	//获取订单的支付方式
    	$Pay = D(ucfirst($oInfo['pay_code']),'Payment');
        D('Qa')->add(array('q'=>2,'a'=>3)); 
    	
    	//echo '==================================';
    	
    	//调用该支付的支付通知函数判断支付是否成功
    	$is_pay_up = $Pay->callback( $Notify );
//    	$oInfo['is_pay_up'] = $is_pay_up?1:0;
    	//echo '==================================';
    	
    	if($Notify)
    	sf($is_pay_up,"C:\\paylog\\data_is_pay_up_notify.php");
		else
    	sf($is_pay_up,"C:\\paylog\\data_is_pay_up.php");
		//echo "-------------------------------------------------";
    	//vde($is_pay_up);
    	
    	//对支付结果进行处理
    	//判断当前订单是否已经处理过
    	if( $is_pay_up && $oInfo['pay_status']!=2 ){	//付款成功，执行修改订单状态等操作
    		//=============================付款成功处理=================================
    		//订单状态修改
                D('Qa')->add(array('q'=>3,'a'=>4)); 
    		$ret = D('Order')->updateOrderPaySuccessStatus( $order_width_suffix );
    		//=========================================================================
    	}
    
    	
    	if( $is_pay_up && $Notify ){		//服务器回调
    		$Pay->paySuccess();
    		exit;
    	}
    	else
    	{
    		return $oInfo;
    	}
    }
    
    //POST	服务器回调
    public function notify(){
    	   	
		sf($_SERVER,"C:\\paylog\\s_n.php");
		sf($_POST,"C:\\paylog\\p_n.php");
		sf($_GET,"C:\\paylog\\g_n.php");
		
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		sf($xml,"C:\\paylog\\x.php");
		
		$this->do_callback( true );
    }
    
    //GET	浏览器回调
    public function callback(){
		sf($_SERVER,"C:\\paylog\\s.php");
		sf($_POST,"C:\\paylog\\p.php");
		sf($_GET,"C:\\paylog\\g.php");

		$oInfo = $this->do_callback( false );
		$this->oInfo = $oInfo;
		$this->display();
    }

}