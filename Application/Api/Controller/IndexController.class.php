<?php
namespace Api\Controller;

/**
 * 接口概览
 */
class IndexController extends ApiController {
	
	
	private function getCont(){
		//所有接口树形列表
		$this->interfaceList = D('Api')->getInterfaceTree();
		
		//
		if( I('id') ){
			$this->cont = $this->interfaceCont( I('id') );
		}else{
			
		}
	}

	//接口内容详情
	public function interfaceCont($id){
	
		$info = D('Api')->getInfo($id);
		$info['request_field'] 	= json_decode($info['request_field'],true);
		$info['return_field'] 	= json_decode($info['return_field'],true);
		
		$info['request_field'] 	= array_merge(C('API_COM_REQUEST_FIELD'), $info['request_field']);
		$info['return_field'] 	= array_merge(C('API_COM_RETURN_FIELD'), $info['return_field']);
		
		if( $info['is_pagination'] ){	//分页接口
			$info['request_field'] 	= array_merge($info['request_field'],C('PAGINATION_FIELD'));
		}
		
		
		//vd( $API_COM_REQUEST_FIELD );
		//vd( $info['request_field'] );
		
		$this->interfaceCont = $info;
		
		$this->assign('info',$info);
		return $this->fetch('main');
	}
	
	
	
	//----------------------------------------------------------------------------
	
	public function index(){
		
		$this->getCont();
		$this->display();
	}
        
	
	
	
	//接口模拟发送界面
	public function demo(){
		$this->getCont();
		
		
		//vd( $this->interfaceCont );
		
		//所有接口树形列表
		$this->index();
	}
	
        public function schedule(){
            endNoticeLive();
            liveStartSoon();
        }

}