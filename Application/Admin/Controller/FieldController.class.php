<?php
namespace Admin\Controller;
/**
 * 幻灯片控制器
 * @author mingling <mingliang@51shanjian.com>
 */
class FieldController extends AdminController {
	/**
	 * 首页幻灯片
	 */
	public function index(){
		$Model = D('Field');
		C('LIST_ROWS',6);
		$map=array('status' =>1 );
		$list = $this->lists($Model,$map,'order_sort desc,id desc');
		$this->assign('list',$list);
		$this->meta_title = '视野列表';
		Cookie('__forward__',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	/**
	 * 编辑pc端
	 */
	public function edit(){
	    if(IS_POST){
                
                if(!$_POST['pic_id']){
                    $this->error('图片必须');
                }
                $data = D('Field')->create();
	        if($data){
	            if($data['id']==''){
	                if(D('Field')->add($data)){
	                    $this->success('添加成功',cookie('__forward__'));
	                } else {
	                    $this->error('添加失败');
	                }
	            } else {
	                if(D('Field')->save($data)){
	                    $this->success('更新成功',cookie('__forward__') );
	                } else {
	                    $this->error('更新失败');
	                }
	            }
	        }else{
	            $this->error(D('Field')->getError());
	        }
	    } else {
	        $id=I('id');
	        if($id!=null)  $this->info=D('Field')->where(array('id'=>$id))->find();
	        $this->display();
	    }
	}
	
	//保存幻灯片
	public function save(){
		$this->jumpUrl = U('Field/index');
		$this->do_edit( D('Field') );
	}
	
	//删除幻灯片
	public function del(){
		$this->do_del( D('Field'),array('id'=>I('get.id')) );
	}
	
	
}