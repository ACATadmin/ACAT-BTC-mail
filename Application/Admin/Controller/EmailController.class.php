<?php
namespace Admin\Controller;

class EmailController extends AdminController {
    
    public function index()
    {
        $prefix = C('DB_PREFIX');
        $title       =   I('title');
        $group = I('group');
        $map['status']  =   array('egt',0);
        if($title){
            $map['email']    =   array('like', '%'.$title.'%');
        }
        if($group)
        {
            $map['group'] = $group;
        }
        $model = D('Email');
        $field = true;
        $list = $this->lists($model,$map,'create_time desc',null,$field);
        int_to_string($list);
        $this->assign('list', $list);
        $this->meta_title = '邮件信息';
	    Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->display();
    }
	/**
	 * 编辑pc端
	 */
	public function add()
	{
	    if(IS_POST)
	    {
            $data = D('Email')->create();
	        if($data){
	            if(!$data['id'])
	            {
	                if(D('Email')->add($data))
	                {
	                    $this->success('添加成功',cookie('__forward__'));
	                } 
	                else 
	                {
	                    $this->error('添加失败');
	                }
	            } 
	            else 
	            {
	                if(D('Email')->save($data))
	                {
	                    $this->success('更新成功',cookie('__forward__') );
	                } 
	                else 
	                {
	                    $this->error('更新失败');
	                }
	            }
	        }
	        else
	        {
	            $this->error(D('Email')->getError());
	        }
	    } 
	    else 
	    {
	        $id=I('id');
	        if($id!=null)  
	        {    
	            $this->info=D('Email')->where(array('id'=>$id))->find();
	        }
            $this->display();
	    }
	}
	
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbid':
                $this->forbid('Email', $map );
                break;
            case 'resume':
                $this->resume('Email', $map );
                break;
            case 'delete':
                $this->delete('Email', $map );
                break;
            default:
                $this->error('参数非法');
        }
    }
    
}