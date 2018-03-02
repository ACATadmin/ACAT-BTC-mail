<?php
namespace Admin\Controller;
/**
 * 支付方式管理
 * @author wei
 *
 */
class PaymentController extends AdminController {

	//============================================================================
	//系统存在的支付方式列表
	public function payment_list(){
		
		//查询数据库中已经安装的支付组建
		C('LIST_ROWS',9999);
		$installedPayment = $this->lists(D('Payment'),array(),'pay_order desc');

		//所有可供调用的支付组件
		$payment_list = D('Payment')->combination( $installedPayment );
		
		echo "<hr/>";
		vd($payment_list);
				
		//组合显示
		
	}
	
	
	public function payTest(){
		
		$pay_code = I('pay_code');
		$Pay = D( ucfirst($pay_code), 'Payment');
		
		if( IS_POST ){			
			//echo C('PAY_CALLBACK_URL');

			$order = $_POST;
			//生成支付代码
			$Pay->getPayCode( $order );
			
			exit;
		}
		
		$this->info = array(
				'order_sn'		=>	date('YmdHis').rand(100000, 999999),
				'subject'		=>	'订单名称',
				'order_amount'	=>	'0.01',
				'body'			=>	'这是一个测试订单',
				'show_url'		=>	'http://www.51shanjian.com/',
		);
		
		$this->display();
	}
		
	//============================================================================
	//支付方式列表
	public function index(){

		//查询数据库中已经安装的支付组建
		C('LIST_ROWS',9999);
		$installedPayment = $this->lists(D('Payment'),array(),'pay_order desc');
                
		//所有可供调用的支付组件
		$payment_list = D('Payment')->combination( $installedPayment );	
                
		
		$this->assign('payment_list',$payment_list);
		$this->display();
	}
		
	//编辑支付
	public function edit(){
		$Payment = D('Payment');
		if( I('get.pay_code') ){
			$info = $Payment->getInfo( array('pay_code'=>I('get.pay_code')) );
			//check_document_position();
			//vde($info['config']);
			//vde($info);
			$this->assign('info',$info);	
		}else if( IS_POST ){
                        $this->jumpUrl 	= U('index');			
			$dbField 		= $Payment->getDbFields();	//数据表中的字段
                        $config 		= array();
			foreach( $_POST as $key => $value ){
				if( !in_array($key, $dbField) ){
					$config[ $key ] = $value;
				}
			}
			$_POST['pay_config'] = serialize($config);
                        $this->do_edit($Payment,array('安装','编辑'));
		}
                switch(I('get.pay_code')){
                    case 'alipay':
                        $view='alipay_edit';
                        break;
                    case 'wxpay':
                        $view='wxpay_edit';
                        break;
                }
		$this->display($view);
	}
	
	//删除
	public function del(){
		$this->do_del( D('Payment'),array('pay_code'=>I('get.pay_code')) );
	}

	
	
	
	//============================================================================
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//银行分类
	public function bank(){
		$bank_list = $this->lists(D('Bank'),array(),'bank_id desc');
		$this->assign('bank_list',$bank_list);
		$this->display();
	}

	//编辑
	public function edit_bank(){
		$Bank = D('Bank');
		if( I('get.id') ){
			$info = $this->getInfo($Bank, I('get.id'),1 );
		}else if( IS_POST ){
			$this->jumpUrl = U('bank');
			$this->do_edit($Bank);
		}
		$this->display();
	}

	//删除
	public function del_bank(){
		$this->do_del( D('Bank'),I('get.id') );
	}
	
	
}
