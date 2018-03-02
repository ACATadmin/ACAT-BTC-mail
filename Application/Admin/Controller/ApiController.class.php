<?php
namespace Admin\Controller;
/**
 * 
 * @author 行者
 *
 */
class ApiController extends AdminController {
	
	//=============================演出管理=============================//
	/**
	 * Api列表
	 */
	public function index(){
		$map = array();
		if(I('api_name')){
			$map['api_name'] 	 	= array('like', '%'.I('api_name').'%');
		}
		if( I('api_group_id') ){
			$map['api_group_id'] 	= I('api_group_id');
		}
		if( isset($_GET['developer']) ){
			$map['developer'] 		= I('developer');
		}
		if( isset($_GET['development_status']) ){
			$map['development_status'] = I('development_status');
		}
		$this->assign('map',$map);
		
		$this->list = $this->lists (D('Api'),$map,'api_group_id desc');
		$this->api_group_list = D('apiGroup')->select();
		//vd($this->list);
		// 记录当前列表页的cookie
		Cookie('__forward__',$_SERVER['REQUEST_URI']);
		
		$this->display();
	}
	
	/**
	 * 新增Api列表
	 */
	public function apiAdd(){
		if( IS_POST ){
			$_POST['request_field'] = transformFormArrToJson( $_POST['request_field'] );
			$_POST['return_field'] 	= transformFormArrToJson( $_POST['return_field'] );
			
			$this->do_edit( D('api'),null,Cookie('__forward__') );
		}
		$this->apiGroupList = D('apiGroup')->order('sort desc')->select();
		$this->display('apiEdit');
	}
	
	/**
	 * 编辑Api
	 */
	public function apiEdit(){
			
		$this->info = $this->getInfo (D('api'),I('id') );
	
		$this->apiAdd();
	}
	
	/**
	 * 删除Api
	 */
	public function apiDel(){
		$this->do_del( D('api') );
	}
	
	
	//=============================Api管理=============================//
	/**
	 * Api分组列表
	 */
	public function apiGroup(){
		if(I('api_group_name')){
			$map['api_group_name'] = array('like', '%'.I('api_group_name').'%');
		}
		$this->list = $this->lists (D('apiGroup'),$map,'id desc');
		$this->display('apiGroup');
	}
	
	/**
	 * 新增Api分组
	 */
	public function apiGroupAdd(){
		if( IS_POST ){
			$this->do_edit( D('apiGroup'),null,U('api/apiGroup') );
		}
		$this->display('apiGroupEdit');
	}
	
	/**
	 * 编辑Api分组
	 */
	public function apiGroupEdit(){
		$this->info = $this->getInfo (D('apiGroup'),I('id') );
		$this->apiGroupAdd();
	}
	
	/**
	 * 删除Api分组
	 */
	public function apiGroupDel(){
		$this->do_del( D('apiGroup') );
	}
	
	
	
	
}