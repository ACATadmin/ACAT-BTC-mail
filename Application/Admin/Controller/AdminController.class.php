<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\CombaseController;
use Admin\Model\AuthRuleModel;
use Admin\Model\AuthGroupModel;
/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class AdminController extends CombaseController {

    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
        
        
        
        
        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }
        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置

        // 是否是超级管理员
        define('IS_ROOT',   is_administrator());
        if(!IS_ROOT && C('ADMIN_ALLOW_IP')){
            // 检查IP地址访问
            if(!in_array(get_client_ip(),explode(',',C('ADMIN_ALLOW_IP')))){
                $this->error('403:禁止访问');
            }
        }
        // 检测系统权限
        if(!IS_ROOT){
            $access =   $this->accessControl();
            if ( false === $access ) {
                $this->error('403:禁止访问');
            }elseif(null === $access ){
                //检测访问权限
                $rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问!');
                }else{
                    // 检测分类及内容有关的各项动态权限
                    $dynamic    =   $this->checkDynamic();
                    if( false === $dynamic ){
                        $this->error('未授权访问!');
                    }
                }
            }
        }        

        
        
        $this->assign('__MENU__', $this->getMenus());
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type=AuthRuleModel::RULE_URL, $mode='url'){
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \Think\Auth();
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则表示权限不明
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic(){}


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl(){
        $allow = C('ALLOW_VISIT');
        $deny  = C('DENY_VISIT');
        $check = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }


    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=CONTROLLER_NAME){
       // $menus  =   session('ADMIN_MENU_LIST.'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            if(!C('DEVELOP_MODE')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menus['main']  =   M('Menu')->where($where)->order('sort asc')->field('id,title,url,icon_class')->select();
            foreach ($menus['main'] as $key => $item) {
                // 判断主菜单权限
                if ( !IS_ROOT && !$this->checkRule(strtolower(MODULE_NAME.'/'.$item['url']),AuthRuleModel::RULE_MAIN,null) ) {
                    unset($menus['main'][$key]);
                    continue;//继续循环
                }
                if(strtolower(CONTROLLER_NAME.'/'.ACTION_NAME)  == strtolower($item['url'])){
                    $menus['main'][$key]['class']='current';
                }
            }

            foreach ($menus['main'] as $key => $item) {
                //生成child树
                $map_group['status'] = 1;
                $map_group['hide'] = 0;
                $map_group['group'] = array('neq','');
                $map_group['pid'] = $item['id'];
                $groups = M('Menu')->where($map_group)->distinct(true)->getField("group",true);
                if(empty($groups))
                {
                    continue;
                }
                //获取二级分类的合法url
                $where          =   array();
                $where['pid']   =   $item['id'];
                $where['hide']  =   0;
                if(!C('DEVELOP_MODE')){ // 是否开发者模式
                    $where['is_dev']    =   0;
                }
                $second_urls = M('Menu')->where($where)->getField('id,url');

                if(!IS_ROOT){
                    // 检测菜单权限
                    $to_check_urls = array();
                    foreach ($second_urls as $key2=>$to_check_url) {
                        if( stripos($to_check_url,MODULE_NAME)!==0 ){
                            $rule = MODULE_NAME.'/'.$to_check_url;
                        }else{
                            $rule = $to_check_url;
                        }
                        if($this->checkRule($rule, AuthRuleModel::RULE_URL,null))
                            $to_check_urls[] = $to_check_url;
                    }
                }
                // 按照分组生成子菜单树
                foreach ($groups as $g) {
                    $map = array('group'=>$g);
                    if(isset($to_check_urls)){
                        if(empty($to_check_urls)){
                            // 没有任何权限
                            continue;
                        }else{
                            $map['url'] = array('in', $to_check_urls);
                        }
                    }
                    $map['pid']     =   $item['id'];
                    $map['hide']    =   0;
                    if(!C('DEVELOP_MODE')){ // 是否开发者模式
                        $map['is_dev']  =   0;
                    }
                    $menuList = M('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                    
                    foreach($menuList as $k => $v)
                    {
                        if(strtolower(CONTROLLER_NAME.'/'.ACTION_NAME)  == strtolower($v['url'])){
                            $menuList[$k]['class']='current';
                            $menus['main'][$key]['class']='current';
                        }
                    }
                    
                    $menus['main'][$key]['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                }
            }
                
            session('ADMIN_MENU_LIST.'.$controller,$menus);
        }
        //vde($menus['main']);
        return $menus['main'];
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            //zyx
            $where = 'hide=0 or id=93';
            $list = M('Menu')->field('id,pid,title,url,tip,hide')->where( $where )->order('sort asc')->select();
            //
            //$list = M('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $list[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            //zyx
            $where = 'hide=0 or id=93';
            $nodes = M('Menu')->field('title,url,tip,pid')->where( $where )->order('sort asc')->select();
            //
//            $nodes = M('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $nodes[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }


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
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    /* protected function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
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

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    } */

    /**
     * 处理文档列表显示
     * @param array $list 列表数据
     * @param integer $model_id 模型id
     */
    protected function parseDocumentList($list,$model_id=null){
        $model_id = $model_id ? $model_id : 1;
        $attrList = get_model_attribute($model_id,false,'id,name,type,extra');
        // 对列表数据进行显示处理
        if(is_array($list)){
            foreach ($list as $k=>$data){
                foreach($data as $key=>$val){
                    if(isset($attrList[$key])){
                        $extra      =   $attrList[$key]['extra'];
                        $type       =   $attrList[$key]['type'];
                        if('select'== $type || 'checkbox' == $type || 'radio' == $type || 'bool' == $type) {
                            // 枚举/多选/单选/布尔型
                            $options    =   parse_field_attr($extra);
                            if($options && array_key_exists($val,$options)) {
                                $data[$key]    =   $options[$val];
                            }
                        }elseif('date'==$type){ // 日期型
                            $data[$key]    =   date('Y-m-d',$val);
                        }elseif('datetime' == $type){ // 时间型
                            $data[$key]    =   date('Y-m-d H:i',$val);
                        }
                    }
                }
                $data['model_id'] = $model_id;
                $list[$k]   =   $data;
            }
        }
        return $list;
    }
    
    
    //获取最新消息
    public function get_Message(){
        
        $mnr = D('MessageNoticeRecord');
        
        $feedback_record = $mnr->where( array('aim_type'=>1) )->getField('aim_id');
        $tip_off_record = $mnr->where( array('aim_type'=>2) )->getField('aim_id');
        
        $lastest_feedback = D('Feedback')->order('id desc')->getField('id');
        $lastest_tip_off = D('TipOff')->order('id desc')->getField('id');
        
        $notice_arr = array();
        
        if($lastest_feedback>$feedback_record){
            $notice_arr[] = array('type'=>'feedback_notice','has_new'=>1);
        }else{
            $notice_arr[] = array('type'=>'feedback_notice','has_new'=>0);
        }
        
        if($lastest_tip_off>$tip_off_record){
            $notice_arr[] = array('type'=>'tip_off_notice','has_new'=>1);
        }else{
            $notice_arr[] = array('type'=>'tip_off_notice','has_new'=>0);
        }
        
        echo json_encode($notice_arr);
    }
    //
}
