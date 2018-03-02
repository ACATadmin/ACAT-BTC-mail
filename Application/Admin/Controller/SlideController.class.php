<?php
namespace Admin\Controller;
/**
 * 幻灯片控制器
 * @author mingling <mingliang@51shanjian.com>
 */
class SlideController extends AdminController {
	/**
	 * 首页幻灯片
	 */
	public function index(){
		$Slide = D('Slide');
		C('LIST_ROWS',6);
		$map=array('platform' =>I('platform'),'type'=>1 );
		$slide_list = $this->lists($Slide,$map,'order_sort desc,slide_id desc');
		$this->assign('slide_list',$slide_list);
		$this->meta_title = '移动端banner图片';
		Cookie('__forward__',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	public function pcslide(){
	    $Slide = D('Slide');
	    C('LIST_ROWS',6);
	    $map=array('platform' =>I('platform'),'type'=>2 );
	    $slide_list = $this->lists($Slide,$map,'order_sort desc,slide_id desc');
	    $this->assign('slide_list',$slide_list);
	    $this->meta_title = 'pc端banner图片';
	    Cookie('__forward__',$_SERVER['REQUEST_URI']);
	    $this->display();
	}
	/**
	 * 编辑pc端
	 */
	public function edit(){
	    if(IS_POST){
                if( $_POST['target_type']!=0 && $_POST['target_type']!=4  && $_POST['target_id']==0){
                    $this->error('请选择跳转目标');
                }
                
                if(!$_POST['slide_title']){
                    $this->error('标题必须');
                }
                
                if(!$_POST['picture_id']){
                    $this->error('图片必须');
                }
                
                if($_POST['target_type']==4 && !$_POST['slide_url']){
                    $this->error('跳转链接必须');
                } 
                
                $data = D('Slide')->create();
	        if($data){
	            if($data['slide_id']==''){
	                if(D('Slide')->add($data)){
	                    $this->success('添加成功',cookie('__forward__'));
	                } else {
	                    $this->error('添加失败');
	                }
	            } else {
	                if(D('Slide')->save($data)){
	                    $this->success('更新成功',cookie('__forward__') );
	                } else {
	                    $this->error('更新失败');
	                }
	            }
	        }else{
	            $this->error(D('Slide')->getError());
	        }
	    } else {
	        $id=I('slide_id');
	        if($id!=null)  $this->info=D('Slide')->where(array('slide_id'=>$id))->find();
                
                $this->article_list = D('Article')->where( array('check_status'=>1,'status'=>1) )->order('pass_time desc')->select();
                $this->video_list = D('Video')->where( array('check_status'=>1,'status'=>1) )->order('pass_time desc')->select();
                $this->notice_live_list = D('Live')->where( array('type'=>2,'live_status'=>0,'status'=>1) )->order('start_time_string asc')->select();
                
                $this->display();
	    }
	}
	
	//保存幻灯片
	public function saveSlide(){
		$this->jumpUrl = U('Slide/index');
		$this->do_edit( D('Slide') );
	}
	
	//删除幻灯片
	public function delSlide(){
		$this->do_del( D('Slide'),array('slide_id'=>I('get.slide_id')) );
	}
	
	
}