<?php

namespace Admin\Controller;

/**
 * 用户账目
 */
class AccountLogController extends AdminController {
	
	//账目明细
	public function info(){
//		sync_account_info(I('uid'));
		$uid = I('uid');
		$AccountLog = D('AccountLog');
		$this->list = $this->lists( $AccountLog, array( 'uid'=> $uid ) ,'change_time DESC');
		$this->accountSum = $AccountLog->get_sum( $uid ,'user_money,frozen_money' );
		$this->display();
	}
	
	//解冻账户冻结金额
	public function unFreezeMoney( $id ){
		//就是将冻结金额里的钱移动到可用金额里
		$ret = D('AccountLog')->unFreezeMoney( $id );
		$this->do_ret($ret,"账户记录解冻");
	}
	
	//调整账目
	public function adjust(){
            
		$AccountLog = D('AccountLog');
		$uid = I('uid');
                
		if( IS_POST ){
                    if( C('ADJUST_PASSWORD')!=I('adjust_password') ){
                            $this->error('调节密码有误');
                    }
                    $_POST['change_type'] 	= 98;		//管理员调节
                    $_POST['uid'] 		= $uid;
                    $_POST['user_money'] 	*= $_POST['add_sub_user_money'];
                    $_POST['rank_points'] 	*= $_POST['add_sub_rank_points'];
                    $_POST['pay_points'] 	*= $_POST['add_sub_pay_points'];
                    $this->jumpUrl = null;
                    if( $this->do_edit($AccountLog , array('新增','修改') , false ) ){
                        $this->jumpUrl = U('AccountLog/info',array('uid' => $uid));
                        $this->do_ret(sync_account_info($uid));
                    }else{
                        $this->do_ret(false);
                    }
                    exit();
                }
		
		$this->accountSum = $AccountLog->get_sum( $uid ,'user_money,rank_points,pay_points' );
		$this->display();
	}
	
	
}
