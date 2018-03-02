<?php
/**
 * 获取并设置订单状态
 * @param unknown $order_id
 */
function getSetOrderStatus($order_id,$isset=0){
	$orderInfo = D('OrderInfo')->where( array('order_id'=>$order_id) )->find();
	
	$order_status = 0;

	if( $orderInfo['pay_status']!=2 ){				//未付款
		$order_status = 0;
	}else if( $orderInfo['rejected_status']>0 ){	//存在退货操作
		$order_status = $orderInfo['rejected_status']+4;
	}else if( $orderInfo['shipping_status']==0 ){	//未发货
		$order_status = 1;
	}else if( $orderInfo['shipping_status']==1 ){	//已发货
		$order_status = 2;
	}else if( $orderInfo['shipping_status']==2 ){	//已完成 
		$order_status = 3;
	}
	
	if( $isset ){
		D('OrderInfo')->where( array('order_id'=>$order_id) )->setField('order_status',$order_status);
	}
	
	return $order_status;
}














