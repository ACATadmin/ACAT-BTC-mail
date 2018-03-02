<?php
namespace Admin\Controller;
/**
 * @author 行者
 */
class AppVersionController extends AdminController {
	
	//项目版本管理
	//-----------------------------------------------------------------------------------------------
	public function index($project_id=0,$server_level=0){
		$map = array();
		if( $server_level ){
			$map['server_level'] = array('like', '%'.I('server_level').'%');
		}
		
		$modal = D('AppVersion');
		
		$this->list = $this->lists ($modal,$map,'id desc',null,$field);
		
		
		$this->display();
	}
	
	/**
	 * 新增项目版本
	 */
	public function addProjectVersion(){
		if( IS_POST ){
                    
		    if(empty($_POST['is_force']))
		    {
		        $_POST['is_force'] = 0;
		    }
			$_POST['up_date'] = date('Y-m-d H:i:s');
			$ret = $this->do_edit( D('AppVersion'),null,false );
			$this->do_ret($ret,null,U('index'));
		}
		$this->display('editProjectVersion');
	}
	
	/**
	 * 编辑项目版本
	 */
	public function editProjectVersion(){
		$this->info = $this->getInfo (D('AppVersion'),I('id'));
		$this->addProjectVersion();
	}
	
	/**
	 * 删除项目版本
	 */
	public function delProjectVersion(){
		$this->do_del( D('AppVersion') );
	}
	
	
	
	
}