<?php
namespace Common\Controller;
use Think\Controller;
use \Tools\Wechat;
use User\Api\UserApi;

/**
 * 通用基控制器
 */
class CombaseController extends Controller {
	
	//===================================================================================================
	//连接查询扩展模型
	public $modelAliasName  = 't1';
	public $extendModel 	= '';
	public $field			= '';
	public $fieldExcept		= false;
	public $baseModel		= '';		//基础模型
	
	
	public function baseModel($baseModel){
		$this->baseModel = $baseModel;
		return $this;
	}
		
	protected function _initialize(){}


	/**
	 * 通用分页列表数据集获取方法
	 *
	 *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
	 *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
	 *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
	 *
	 * @param sting|Model  $model   模型名或模型实例
	 * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
	 * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
	 *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
	 *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
	 *
	 * @param array        $base    基本的查询条件
	 * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
	 * @author 朱亚杰 <xcoolcc@gmail.com>
	 *
	 * @return array|false
	 * 返回数据集
	 */
	protected function lists ($original_model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true,$list_count=null){
		
		$_GET['p'] = I('p');
		
		$options    =   array();
		$REQUEST    =   (array)I('request.');
	
		if( I('page_size') ){
			C('LIST_ROWS',I('page_size') );
		}
		
		
		if(is_string($original_model)){
			$model  =   D($original_model);
		}else{
			$model 	= 	$original_model;
		}
	
		$OPT        =   new \ReflectionProperty($model,'options');
		$OPT->setAccessible(true);
	
		$pk         =   $model->getPk();
	
	
		//-----------------------------------------------------------------
		if( $this->extendModel ){
			if( isset($base['status']) ){
				$base[ $this->modelAliasName.'.status' ] = $base['status'];
				unset($base['status']);
			}
			$pk = $this->modelAliasName.'.'.$pk;
		}
		if( $this->field ){
			$field = $this->field;
		}
		//-----------------------------------------------------------------
	
	
		if($order===null){
			//order置空
		}else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
			$options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
		}elseif( $order==='' && empty($options['order']) && !empty($pk) ){
			$options['order'] = $pk.' desc';
		}elseif($order){
			$options['order'] = $order;
		}
		unset($REQUEST['_order'],$REQUEST['_field']);
	
		$options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
			if($val===''||$val===null){
				return false;
			}else{
				return true;
			}
		});
		if( empty($options['where'])){
			unset($options['where']);
		}
		$options      =   array_merge( (array)$OPT->getValue($model), $options );
	
		
		
		
		
		//满足条件的查询数据总数
		if( $list_count ){
			$total 		  = $list_count;
		}else if( $this->extendModel ){	//多表查询
			$total        =   M()	->table( C('DB_PREFIX').$original_model.' AS '.$this->modelAliasName)
									->join( $this->extendModel )
									->where($options['where'])
									->count();
		}else{
			$model_clone  	= clone $model;
			$total        	= $model->where($options['where'])->count();
			$getLastSql 	= $model->getLastSql();// . "<br/>";
			//echo $getLastSql;echo "<br/>";
			
			$is_have_group = strstr($getLastSql, 'GROUP BY');
			
			//vd($options);
			//echo $total.'|--'.$pos.'--';
			//vd($is_have_group);vd($this->notUseGroupCount);
			if( $is_have_group && $this->notUseGroupCount!=true ){
				$total = count( $model_clone->where( $options['where'] )->field( 'count(*)' )->order('')->select() );
				//echo $model_clone->getLastSql();
			}			
		}
	
		//echo $total . "<br/>";
		
		if( isset($REQUEST['r']) ){
			$listRows = (int)$REQUEST['r'];
		}else{
			//echo __LINE__ . "<br>";
			$listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
		}
		//echo $listRows . "<br>";
		$page = new \Think\Page($total, $listRows, $REQUEST);
		if($total>$listRows){
			$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
		}
		$p =$page->show();
		$this->assign('_page', $p? $p: '');
		$this->assign('_total',$total);
		$options['limit'] = $page->firstRow.','.$page->listRows;
		//echo "<br/>".$total.'==='.$listRows.'==='.$p."<br/>";
		//vde($p);
		$model->setProperty('options',$options);
		//vd($options);
	
		//获取查询出来的数据
		if( $this->extendModel ){	//多表查询
			$list =		$model	->table( C('DB_PREFIX').$original_model.' AS '.$this->modelAliasName)
								->join( $this->extendModel )
								->where($options['where'])
								->field($field,$this->fieldExcept)
								->select();
		}else{
			//echo __LINE__ . "<br>";
			//$list = $model->field($field)->select();
			$list = $model->field($field,$this->fieldExcept)->select();
		}
	
		if($this->ss){
			echo $model->getLastSql();
		}
		//echo $model->getLastSql();
		//vd($list);
				
		$model = (isset($this->baseModel)&&!empty($this->baseModel))?$this->baseModel:$model;
		if( method_exists($model,'formatList') ){
			$list = $model->formatList($list);
		}
	
		return $list;
	}
	public function setModelListField( $Model ){
		$this->field 		= $Model->getListFields;
		$this->fieldExcept 	= $Model->getListFieldsExcept;
		return $this;
	}
	

	/**
	 * 增改通用
	 */
	public function do_edit( $Model ,$messageArr=array('新增','修改'),$jump=1){
	
	    $ret = 0;
	    $Pk = $Model->getPk();
	    $data = $Model->create();
	    //vde($data);
	
	    if( $data ){
	        if( IS_POST ){
	            if( $_POST[$Pk] ){
	                $ret = $Model->save();
	                if( $ret ){
	                    $ret = $_POST[$Pk];
	                }
	                $message = $messageArr[1];
	            }else{
	                $ret = $Model->add();
	                $message = $messageArr[0];
	            }
	            //echo $Model->getLastSql();exit;
	            //$this->error( $Model->getError() );
	        }
	    }else{
	        $this->error( $Model->getError() );
	        exit;
	    }
	
	    //$ret 成功返回主键ID
	
	    return $this->do_ret( $ret, $message, $jump );
	}
	/**
	 * 删除通用
	 */
	public function do_del( $Model , $map, $message='删除',$jump=1){
	
	    if( !is_array($map) ){
	        $Pk = $Model->getPk();
	        	
	        if( empty($map) ){
	            $map = I( $Pk );
	        }
	        	
	        $map = array($Pk=>$map);
	    }
	    $ret = $Model->where( $map )->delete();
	    //echo $Model->getLastsql();echo "<hr/>";exit;
	    return $this->do_ret( $ret, $message, $jump );
	}
	/**
	 * 跳转通用
	 */
	public function do_ret( $ret, $message='操作', $jump=1 ){
	
	    if( $jump || $this->jumpUrl ){
	        if( $jump===1 ){
	            $this->jumpUrl = $this->jumpUrl?$this->jumpUrl:'';
	        }else{
	            $this->jumpUrl = $jump;
	        }
	        	
	        if( !is_array($message) ){
	            $message .= ($ret)?'成功':'失败';
	        }else{
	            $message = ($ret)?$message[0]:$message[1];
	        }
	        	
	        if( $ret ){
	            $this->success( $message, $this->jumpUrl );
	        }else{
	            $this->error( $message );
	        }
	        exit;
	    }else{
	        return $ret;
	    }
	}
	
	/**
	 * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
	 *
	 * @param string $model 模型名称,供M函数使用的参数
	 * @param array  $data  修改的数据
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 *
	 * @author 朱亚杰  <zhuyajie@topthink.net>
	 */
	final protected function editRow ( $model ,$data, $where , $msg ){
	    $id    = array_unique((array)I('id',0));
	    $id    = is_array($id) ? implode(',',$id) : $id;
	    //如存在id字段，则加入该条件
	    $fields = M($model)->getDbFields();
	    if(in_array('id',$fields) && !empty($id)){
	        $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
	    }
	
	    $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
	    if( M($model)->where($where)->save($data)!==false ) {
	        $this->success($msg['success'],$msg['url'],$msg['ajax']);
	    }else{
	        $this->error($msg['error'],$msg['url'],$msg['ajax']);
	    }
	}
	
	/**
	 * 禁用条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的 where()方法的参数
	 * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 *
	 * @author 朱亚杰  <zhuyajie@topthink.net>
	 */
	protected function forbid ( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
	    $data    =  array('status' => 0);
	    $this->editRow( $model , $data, $where, $msg);
	}
        
        protected function defind ( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
	    $data    =  array('status' => 2);
	    $this->editRow( $model , $data, $where, $msg);
	}
	
	/**
	 * 恢复条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 *
	 * @author 朱亚杰  <zhuyajie@topthink.net>
	 */
	protected function resume (  $model , $where = array() , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！')){
	    $data    =  array('status' => 1);
	    $this->editRow(   $model , $data, $where, $msg);
	}
        
        protected function setStatusNew( $model , $status ,$where = array() ,$fail_reason='', $msg = array( 'success'=>'设置成功！', 'error'=>'设置失败！')){
            $data    =  array('status' => $status,'fail_reason'=>$fail_reason);
            $this->editRow( $model , $data, $where, $msg);
        }
        protected function setsaleStatus( $model , $status ,$where = array() , $msg = array( 'success'=>'设置成功！', 'error'=>'设置失败！')){
            $data    =  array('is_sale' => $status);
            $this->editRow( $model , $data, $where, $msg);
        }
        protected function setcheckStatus( $model , $status ,$where = array() ,$fail_reason='', $msg = array( 'success'=>'设置成功！', 'error'=>'设置失败！')){
            $data    =  array('check_status' => $status,'fail_reason'=>$fail_reason);
            if($status==1){ $data['pass_time']=NOW_TIME; }
            $this->editRow( $model , $data, $where, $msg);
        }
        protected function setonlineStatus( $model , $status ,$where = array() , $msg = array( 'success'=>'设置成功！', 'error'=>'设置失败！')){
            $data    =  array('online' => $status);
            $this->editRow( $model , $data, $where, $msg);
        }
        
        
        protected function setrealnameStatus( $model , $status ,$where = array() ,$fail_reason='', $msg = array( 'success'=>'设置成功！', 'error'=>'设置失败！')){
            $data    =  array('realname_status' => $status,'fail_reason'=>$fail_reason);
            $this->editRow( $model , $data, $where, $msg);
        }
        
        //修改指定表字段状态通用
	protected function changeFieldStatus ( $model , $where = array() , $field='' , $value='' ,$msg = array( 'success'=>'操作成功！', 'error'=>'操作失败！')){
                if($field!=='' && $value!==''){
                    $data    =  array($field => $value);
                    $this->editRow( $model , $data, $where, $msg);
                }
	}
        
        protected function setProfessionRecommondStatus( $model , $status ,$where = array() ,$recommend_type=0,$recommend_sort=0, $msg = array( 'success'=>'设置成功！', 'error'=>'设置失败！')){
            $data    =  array('is_recommend' => $status,'recommend_type'=>$recommend_type,'recommend_sort'=>$recommend_sort);
            $this->editRow( $model , $data, $where, $msg);
        }
        
	/**
	 * 还原条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 * @author huajie  <banhuajie@163.com>
	 */
	protected function restore (  $model , $where = array() , $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！')){
	    $data    = array('status' => 1);
	    $where   = array_merge(array('status' => -1),$where);
	    $this->editRow(   $model , $data, $where, $msg);
	}
	
	/**
	 * 条目假删除
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 *
	 * @author 朱亚杰  <zhuyajie@topthink.net>
	 */
	protected function delete ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
	    $data['status']         =   -1;
	    $this->editRow(   $model , $data, $where, $msg);
	}
	
	/**
	 * 设置一条或者多条数据的状态
	 */
	public function setStatus($Model=CONTROLLER_NAME){
	
	    $ids    =   I('request.ids');
	    $status =   I('request.status');
	    if(empty($ids)){
	        $this->error('请选择要操作的数据');
	    }
	
	    $map['id'] = array('in',$ids);
	    switch ($status){
	        case -1 :
	            $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
	            break;
	        case 0  :
	            $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
	            break;
	        case 1  :
	            $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
	            break;
	        default :
	            $this->error('参数错误');
	            break;
	    }
	}
    

	public function loadMore( $view ){
    	$is_load_more = I('load_more');
    	$list_contents = $this->fetch( $view );
    	if( $is_load_more ){
    		echo $list_contents;
    		exit;
    	}
    	$this->assign('list_contents',$list_contents);
    }
	

    /**
     * 获取信息通用
     * @param 实例化模型 						$Model
     * @param 查询条件，数组或主键 			$map
     * @param 是否向模版赋值，默认直接返回	$assign
     * @return 记录信息
     */
    public function getInfo( $Model , $map ,$assign=0){
    	if( is_array($map) ){		//
    		$info = $Model->where( $map )->find();
    	}else{						//主键检索
    		$Pk = $Model->getPk();
    		$info = $Model->where( array($Pk=>$map) )->find();
    	}
    	//格式化
    	if( method_exists($Model,'format') ){
            $info = $Model->format( $info );
        }
    	//vd($info);
    	//
        
    	if( $assign == 1 ){
    		$this->assign('info',$info);
    	}else if( $assign == 2 ){
    		$this->assign('info',$info);
    		return $info;
    	}else{
            
    		return $info;
    	}
    }
    
}
