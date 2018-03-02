<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// OneThink常量定义
const ONETHINK_VERSION    = '1.1.141101';
const ONETHINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}
function getcatename($cate_id){
    if(!$cate_id) return $cate_id;
    $cateinfo=M('GoodsCategory')->field('cate_name')->find($cate_id);
    return !empty($cateinfo)? $cateinfo['cate_name']:false;
}
function getTypename($type,$pz){
    if(empty($type) && empty($pz)){
        return false;
    }
    $typesarr=C($pz);
    return empty($typesarr) ? false : $typesarr[$type];
}
/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}
function getAreaname($area){
    if(empty($area)){
        return false;
    }
    $areainfo=D('Region')->field('region_name')->find($area);
    return empty($areainfo) ? false : $areainfo['region_name'];
}
/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url){
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url(){
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook,$params=array()){
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name){
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name){
    $class = get_addon_class($name);
    if(class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    }else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()){
    $url        = parse_url($url);
    $case       = C('URL_CASE_INSENSITIVE');
    $addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if(isset($url['query'])){
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons'     => $addons,
        '_controller' => $controller,
        '_action'     => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0){
    static $list;
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if(empty($list)){
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if(isset($list[$key])){ //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
        if($info && isset($info[1])){
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0){
    static $list;
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if(empty($list)){
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if(isset($list[$key])){ //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if($info !== false && $info['nickname'] ){
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null){
    static $list;

    /* 非法分类ID */
    if(empty($id) || !is_numeric($id)){
        return '';
    }

    /* 读取缓存数据 */
    if(empty($list)){
        $list = S('sys_category_list');
    }

    /* 获取分类名称 */
    if(!isset($list[$id])){
        $cate = M('Category')->find($id);
        if(!$cate || 1 != $cate['status']){ //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); //更新缓存
    }
    return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id){
    return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id){
    return get_category($id, 'title');
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id=null){
    $map   = array('status' => 1, 'extend' => 0);
    if(!is_null($model_id)){
        $map['id']  =   array('neq',$model_id);
    }
    $model = M('Model')->where($map)->field(true)->select();
    foreach ($model as $value) {
        $list[$value['id']] = $value;
    }
    return $list;
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null){
    static $list;

    /* 非法分类ID */
    if(!(is_numeric($id) || is_null($id))){
        return '';
    }

    /* 读取缓存数据 */
    if(empty($list)){
        $list = S('DOCUMENT_MODEL_LIST');
    }

    /* 获取模型名称 */
    if(empty($list)){
        $map   = array('status' => 1, 'extend' => 1);
        $model = M('Model')->where($map)->field(true)->select();
        foreach ($model as $value) {
            $list[$value['id']] = $value;
        }
        S('DOCUMENT_MODEL_LIST', $list); //更新缓存
    }

    /* 根据条件返回数据 */
    if(is_null($id)){
        return $list;
    } elseif(is_null($field)){
        return $list[$id];
    } else {
        return $list[$id][$field];
    }
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data){
    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null){

    //参数检查
    if(empty($action) || empty($model) || empty($record_id)){
        return '参数不能为空';
    }
    if(empty($user_id)){
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if($action_info['status'] != 1){
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id']      =   $action_info['id'];
    $data['user_id']        =   $user_id;
    $data['action_ip']      =   ip2long(get_client_ip());
    $data['model']          =   $model;
    $data['record_id']      =   $record_id;
    $data['create_time']    =   NOW_TIME;

    //解析日志规则,生成日志备注
    if(!empty($action_info['log'])){
        if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
            $log['user']    =   $user_id;
            $log['record']  =   $record_id;
            $log['model']   =   $model;
            $log['time']    =   NOW_TIME;
            $log['data']    =   array('user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>NOW_TIME);
            foreach ($match[1] as $value){
                $param = explode('|', $value);
                if(isset($param[1])){
                    $replace[] = call_user_func($param[1],$log[$param[0]]);
                }else{
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] =   str_replace($match[0], $replace, $action_info['log']);
        }else{
            $data['remark'] =   $action_info['log'];
        }
    }else{
        //未定义日志规则，记录操作url
        $data['remark']     =   '操作url：'.$_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if(!empty($action_info['rule'])){
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self){
    if(empty($action)){
        return false;
    }

    //参数支持id或者name
    if(is_numeric($action)){
        $map = array('id'=>$action);
    }else{
        $map = array('name'=>$action);
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();
    if(!$info || $info['status'] != 1){
        return false;
    }

    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = $info['rule'];
    $rules = str_replace('{$self}', $self, $rules);
    $rules = explode(';', $rules);
    $return = array();
    foreach ($rules as $key=>&$rule){
        $rule = explode('|', $rule);
        foreach ($rule as $k=>$fields){
            $field = empty($fields) ? array() : explode(':', $fields);
            if(!empty($field)){
                $return[$key][$field[0]] = $field[1];
            }
        }
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if(!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])){
            unset($return[$key]['cycle'],$return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null){
    if(!$rules || empty($action_id) || empty($user_id)){
        return false;
    }

    $return = true;
    foreach ($rules as $rule){

        //检查执行周期
        $map = array('action_id'=>$action_id, 'user_id'=>$user_id);
        $map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
        $exec_count = M('ActionLog')->where($map)->count();
        if($exec_count > $rule['max']){
            continue;
        }

        //执行数据库操作
        $Model = M(ucfirst($rule['table']));
        $field = $rule['field'];
        $res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));

        if(!$res){
            $return = false;
        }
    }
    return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files){
    foreach ($files as $key => $value) {
        if(substr($value, -1) == '/'){
            mkdir($value);
        }else{
            @file_put_contents($value, '');
        }
    }
}

if(!function_exists('array_column')){
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null){
    if(empty($model_id)){
        return false;
    }
    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);
    if($info['extend'] != 0){
        $name = $Model->getFieldById($info['extend'], 'name').'_';
    }
    $name .= $info['name'];
    return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true,$fields=true){
    static $list;

    /* 非法ID */
    if(empty($model_id) || !is_numeric($model_id)){
        return '';
    }

    /* 获取属性 */
    if(!isset($list[$model_id])){
        $map = array('model_id'=>$model_id);
        $extend = M('Model')->getFieldById($model_id,'extend');

        if($extend){
            $map = array('model_id'=> array("in", array($model_id, $extend)));
        }
        $info = M('Attribute')->where($map)->field($fields)->select();
        $list[$model_id] = $info;
    }

    $attr = array();
    if($group){
        foreach ($list[$model_id] as $value) {
            $attr[$value['id']] = $value;
        }
        $model     = M("Model")->field("field_sort,attribute_list,attribute_alias")->find($model_id);
        $attribute = explode(",", $model['attribute_list']);
        if (empty($model['field_sort'])) { //未排序
            $group = array(1 => array_merge($attr));
        } else {
            $group = json_decode($model['field_sort'], true);

            $keys = array_keys($group);
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    if (!in_array($val['id'], $attribute)) {
                        unset($attr[$key]);
                    }
                }
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }
        if (!empty($model['attribute_alias'])) {
            $alias  = preg_split('/[;\r\n]+/s', $model['attribute_alias']);
            $fields = array();
            foreach ($alias as &$value) {
                $val             = explode(':', $value);
                $fields[$val[0]] = $val[1];
            }
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    if (!empty($fields[$val['name']])) {
                        $value[$key]['title'] = $fields[$val['name']];
                    }
                }
            }
        }
        $attr = $group;
    }else{
        foreach ($list[$model_id] as $value) {
            $attr[$value['name']] = $value;
        }
    }
    return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null){
    if(empty($value) || empty($table)){
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);
    if(empty($field)){
        $info = $info->field(true)->find();
    }else{
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url'){
    $link = '';
    if(empty($link_id)){
        return $link;
    }
    $link = M('Url')->getById($link_id);
    if(empty($field)){
        return $link;
    }else{
        return $link[$field];
    }
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null){
    if(empty($cover_id)){
        return false;
    }
    $picture = M('Picture')->where(array('status'=>1))->getById($cover_id);
    if($field == 'path'){
        if(!empty($picture['url'])){
            $picture['path'] = $picture['url'];
        }else{
            $picture['path'] = __ROOT__.$picture['path'];
        }
    }
    return empty($field) ? $picture : $picture[$field];
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0){
    if(empty($pos) || empty($contain)){
        return false;
    }

    //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if($res !== 0){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取数据的所有子孙数据的id值
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */

function get_stemma($pids,Model &$model, $field='id'){
    $collection = array();

    //非空判断
    if(empty($pids)){
        return $collection;
    }

    if( is_array($pids) ){
        $pids = trim(implode(',',$pids),',');
    }
    $result     = $model->field($field)->where(array('pid'=>array('IN',(string)$pids)))->select();
    $child_ids  = array_column ((array)$result,'id');

    while( !empty($child_ids) ){
        $collection = array_merge($collection,$result);
        $result     = $model->field($field)->where( array( 'pid'=>array( 'IN', $child_ids ) ) )->select();
        $child_ids  = array_column((array)$result,'id');
    }
    return $collection;
}

/**
 * 验证分类是否允许发布内容
 * @param  integer $id 分类ID
 * @return boolean     true-允许发布内容，false-不允许发布内容
 */
function check_category($id){
    if (is_array($id)) {
		$id['type']	=	!empty($id['type'])?$id['type']:2;
        $type = get_category($id['category_id'], 'type');
        $type = explode(",", $type);
        return in_array($id['type'], $type);
    } else {
        $publish = get_category($id, 'allow_publish');
        return $publish ? true : false;
    }
}

/**
 * 检测分类是否绑定了指定模型
 * @param  array $info 模型ID和分类ID数组
 * @return boolean     true-绑定了模型，false-未绑定模型
 */
function check_category_model($info){
    $cate   =   get_category($info['category_id']);
    $array  =   explode(',', $info['pid'] ? $cate['model_sub'] : $cate['model']);
    return in_array($info['model_id'], $array);
}

//创建文件缩略图
function create_zoom( $pic, $image=null){

    if( !$image ){
        $image = new \Think\Image();
    }
    
    $pic_img = '.'.$pic;
    
    //          echo "<br/>";
    //          echo $basename = basename($pic_img);
    //          echo "<br/>";
    //          echo $dirname = dirname($pic_img);
     
    $basename   = basename($pic_img);
    $dirname    = dirname($pic_img).'/zoom';
     
    //判断压缩目录是否存在，不存在则创建
    if( !is_dir($dirname) ){
        mkdir($dirname);
    }
    //小图 
    $image->open( $pic_img );
    $image->thumb(230,400,\Think\Image::IMAGE_THUMB_SCALE)->save( $dirname .'/small_'. $basename );
    //中图
    $image->open( $pic_img );
    $image->thumb(670,1180,\Think\Image::IMAGE_THUMB_SCALE)->save( $dirname .'/meddle_'. $basename );
}

//创建文件缩略图
function create_zoom_same( $pic, $image=null){

    if( !$image ){
        $image = new \Think\Image();
    }
    
    $pic_img = '.'.$pic;
    
    //          echo "<br/>";
    //          echo $basename = basename($pic_img);
    //          echo "<br/>";
    //          echo $dirname = dirname($pic_img);
     
    $basename   = basename($pic_img);
    $dirname    = dirname($pic_img).'/zoom';
     
    //判断压缩目录是否存在，不存在则创建
    if( !is_dir($dirname) ){
        mkdir($dirname);
    }

    //中图
    $image->open( $pic_img );
    $image->save( $dirname .'/'. $basename );
}

//格式化时间
function date_time_format($time,$sign){
    $time=  date($sign,strtotime($time));
    return $time;
}

//获取时间差
function get_difference_time($endtime,$starttime=''){
    $starttime=$starttime==''?NOW_TIME:strtotime($starttime);
    
    $endtime=  strtotime($endtime);
    $starttime=  $starttime;
    
    
    //计算天数
    $timediff = $endtime-$starttime;
    $days = intval($timediff/86400);
    //计算小时数
    $remain = $timediff%86400;
    $hours = intval($remain/3600);
    //计算分钟数
    $remain = $remain%3600;
    $mins = intval($remain/60);
    //计算秒数
    $secs = $remain%60;
    $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
    
    return $res;
}

//获取用户头像
function getUserHead( $uid ){
	$headPic = D('Member')->where( array('uid'=>$uid) )->getField('head_pic_id');	
	return getImgUrl($headPic);
}




//极光推送
function json_array( $result ){
    $result_json=json_encode($result);
    return json_decode($result_json,true);
/*     $regid="120c83f76017efc4fe3";
     $data['content']="fdfdsf";
     $result_s = sendNotifySpecial($regid, $data['content']); */
}
function sendNotifyAll( $message ){
    require __DIR__ . '/JPush\JPush.php';
    $app_key=C('APP_KEY');
    $master_secret=C('MASTER_SECRET');
    $client=new JPush($app_key,$master_secret);
    $result=$client->push()->setPlatform('all')->addAllAudience()->setNotificationAlert($message)->send();
    return json_array($result);
}

function sendNotifySpecial( $regid,$message){
    require __DIR__ . '/JPush\JPush.php';
    $app_key=C('APP_KEY');
    $master_secret=C('MASTER_SECRET');
    $client=new JPush($app_key,$master_secret);
    $result=$client->push()->setPlatform('all')->addRegistrationId($regid)->setNotificationAlert($message)->send();
    return json_array($result);
}

function sendSpecialMsg( $regid,$message,$did,$mid){
    require __DIR__ . '/JPush\JPush.php';
    $app_key=C('APP_KEY');
    $master_secret=C('MASTER_SECRET');
    $client=new JPush($app_key,$master_secret);
    $result=$client->push()->setPlatform('all')->addRegistrationId($regid)
    ->addAndroidNotification($message,'',1,array('did'=>$did,'mid'=>$mid))
    ->addIosNotification($message,'','+1',true,'',array('did'=>$did,'mid'=>$mid))->send();
    return json_array($result);
}

function sendSpecialMsgJson( $regid,$message,$param){
    if(!class_exists('JPush')){
        require __DIR__ . '/JPush\JPush.php';
    }
    $app_key=C('APP_KEY');
    $master_secret=C('MASTER_SECRET');
    $client=new JPush($app_key,$master_secret);
    $result=$client->push()->setPlatform('all',$regid)->addRegistrationId($regid)
    ->addAndroidNotification($message,'',1,array('extInfo'=>$param),$regid)
    ->addIosNotification($message,'','+1',true,'',array('extInfo'=>$param),$regid)->send($regid);
    if($result){
        $save_result=  json_encode($result);
        $save_result= json_decode($save_result,true);
        $j_arr['sendno']= $save_result['data']['sendno'];
        $j_arr['msg_id']= $save_result['data']['msg_id'];
        
        $j_arr['jpush_id']=  serialize($regid);
        $param=  json_decode($param,true);
        $j_arr['type']=$param['type'];
        $j_arr['content']=$message;
        $j_arr['jpush_time']=date('Y-m-d H:i:s',NOW_TIME);
        D('JpushLog')->add($j_arr);
    }
    return json_array($result);
}

function reportNotify( $msgIds){
    require __DIR__ . '/JPush\JPush.php';
    $app_key=C('APP_KEY');
    $master_secret=C('MASTER_SECRET');
    $client=new JPush($app_key,$master_secret);
    $response=$client->report()->getReceived($msgIds);
    return json_array($response);
}
//



/**
* 导出excel信息
* @param string  $titles 导出的表格标题
* @param string  $keys 需要导出的键名默认为所有
* @param array  $data 需要导出的数据
* @param string  $file_name 导出的文件名称
*/
function export_excel( $titles = '' , $keys = '' ,$data = array() , $file_name = '导出文件' )
{

       set_time_limit(0);
       Vendor('PHPExcel');
       $file_name = iconv("utf-8", "gb2312", $file_name);
       header("Pragma: public");
       header("Expires: 0");
       header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
       header("Content-Type:application/force-download");
       header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
       header("Content-Type:application/octet-stream");
       header("Content-Type:application/download");
       header('Content-Disposition:attachment;filename='.$file_name.'.xlsx');
       header("Content-Transfer-Encoding:binary");

       $y = 1;
       $s = 0;

       $objPHPExcel = new \PHPExcel();

       //设置表头
       $titles_arr = str2arr($titles);
       foreach ( $titles_arr as $k => $v ){
           $objPHPExcel->setActiveSheetIndex($s)->setCellValue(string_from_column_index($k).$y,$v);
           //
           $objPHPExcel->getActiveSheet()->getStyle(string_from_column_index($k).$y)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
           $objPHPExcel->getActiveSheet()->getStyle(string_from_column_index($k).$y)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
           $objPHPExcel->getActiveSheet()->getStyle(string_from_column_index($k).$y)->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle(string_from_column_index($k).$y)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
           $objPHPExcel->getActiveSheet()->getStyle(string_from_column_index($k).$y)->getFill()->getStartColor()->setARGB('D1D1D1');
           $objPHPExcel->getActiveSheet()->getColumnDimension(string_from_column_index($k))->setWidth(40);
           //
       }

       //设置内容
       $keys_arr = str2arr($keys);


       foreach ($data as $k => $v) {
           $ii = 0;
           foreach ( $v as $kk => $vv ){
               if(!empty($keys) && in_array($kk, $keys_arr)){
                   $num = array_search($kk,$keys_arr);
                   $objPHPExcel->setActiveSheetIndex($s)->setCellValue(string_from_column_index($num).($y+$k+1), $vv );
                   //
                   $objPHPExcel->getActiveSheet()->getStyle(string_from_column_index($num).($y+$k+1))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                   $objPHPExcel->getActiveSheet()->getStyle(string_from_column_index($num).($y+$k+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                   //
                   $ii++;
               }
           }
       }

       $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
       $objWriter->save('php://output');
}

/**
* 处理表格字母列超过26个 转为多字母
*/
function  string_from_column_index( $pColumnIndex = 0 )
{
   static $_indexCache = array();
   if(!isset($_indexCache[$pColumnIndex])){
       if($pColumnIndex < 26){
           $_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
       }elseif($pColumnIndex < 702){
           $_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)).chr(65 + $pColumnIndex % 26);
       }else{
           $_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex - 26) / 676 )).chr(65 + ((($pColumnIndex - 26) % 676) / 26 )).  chr( 65 + $pColumnIndex % 26);
       }
   }
   return $_indexCache[$pColumnIndex];
}



//获取当前登录用户头像
function getLoginUserHead(){
    $uid=  is_login();
    $headPic = D('Member')->where( array('uid'=>$uid) )->getField('head_pic_id');
    return getImgUrl($headPic);
}


//获取封面缩略图
function get_cover_zoom($cover_id, $size='m', $field = 'path'){
    $pic_img = get_cover($cover_id, $field);

    $basename   = basename($pic_img);
    $dirname    = dirname($pic_img).'/zoom';
    
    if( $size == 's' ){
        $ret_img = $dirname .'/small_'. $basename;
    }else if( $size == 'm' ){
        $ret_img = $dirname .'/meddle_'. $basename;
    }else{
        return $pic_img;
    }
    
    /*
    //判断缩略图是否存在
    if( !file_exists($ret_img) && $pic_img ){
        //不存在，则创建文件的缩略图
        create_zoom( $pic_img );
    }
    */
    
    return $ret_img;
}

//获取字符长度
function utf8_strlen($string = null) {
    // 将字符串分解为单元
    preg_match_all('/./us', $string, $match);
    // 返回单元个数
    return count($match[0]);
}


//获取6位随机数
function getSixNumber($max){
    $number='';
    for($i=0;$i<$max;$i++){
        $rand=rand(0,9);
        $number.=$rand;
    }
    return $number;
}

//配置项特殊转换
function formatConfigArray($arr){
    $new_arr=array();
    foreach($arr as $k=>$v){
        $new_arr[$v['id']]=$v['name'];
    }
    return $new_arr;
}

//截取段落
function subtext($text, $length=24)
{
    if(mb_strlen($text, 'utf8') > $length) 
    return mb_substr($text, 0, $length, 'utf8').'...';
    return $text;
}
//

//超过指定数格式化
function numberFormat($number,$max_number,$round=1,$string='万'){
    $return=0;
    if($number>=$max_number){
        $return=$number/$max_number;
    }else{
        return $number;
    }
    if(strpos($return,'.')){
        $return=round($return,$round);
    }
    
    return $return.$string;
}
//


//生成订单编号
function build_order_no(){
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(0,9).rand(0,9).rand(0,9);
}




//获取最近月份开始结束
function getLastestMonth($num,$time=''){
    if($time){
        $start = date('Y-m-01',$time);
        $end = date("Y-m-d",strtotime("$start +1 month"));
    }else{
        $start = date('Y-m-01',NOW_TIME);
        //$one_end = date("Y-m-d",strtotime("$one_start +1 month -1 day"));
        $end = date("Y-m-d",strtotime("$start +1 month"));
    }
    
    $arr = array();
    $arr[] = array('start_time'=>$start,'end_time'=>$end);
    
    for($i=2;$i<=$num;$i++){
        $start = date('Y-m-01',strtotime($start)-C('DAY'));
        $end = date("Y-m-d",strtotime("$start +1 month"));
        $arr[] = array('start_time'=>$start,'end_time'=>$end);
    }
    
    return $arr;
}

//获取月数
function getNumMonth($num){
    
    $start = date('Y-m-01',NOW_TIME);
    //$one_end = date("Y-m-d",strtotime("$one_start +1 month -1 day"));
    
    $arr = array();
    
    for($i=1;$i<=$num;$i++){
        $time_exp = date('Y年m月',strtotime($start));
        $time = date('Y-m',strtotime($start));
        $arr[] = array('time_exp'=>$time_exp,'time'=>$time);
        $start = date('Y-m-01',strtotime($start)-C('DAY'));
    }
    
    return $arr;
}



//环信
function getHuanXinUsernameAndPwd(){
    $username =  randCode(10,2);
    $pwd = 'ysyy123456';
    $CardModel = M('Huanxin');
    $where['hx_username'] = $username;
    $card_info = $CardModel->where($where)->field(true)->find();
    $user = array();
    if(empty($card_info)){
        $user['username'] = $username;
        $user['password'] = $pwd;
        return $user;
    }else{
        return getHuanXinUsernameAndPwd();
    }
}


function file_size_format($size = 0, $dec = 2)
{
    $unit = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    $result['size'] = round($size, $dec);
    $result['unit'] = $unit[$pos];
    return $result['size'] . $result['unit'];
}


//先获取app管理员token POST /{org_name}/{app_name}/token
function _get_token($hxurl,$client_id,$client_secret)
{
    $formgettoken=$hxurl."token";
    $body=array(
        "grant_type"=>"client_credentials",
        "client_id"=>$client_id,
        "client_secret"=>$client_secret
    );
    $patoken=json_encode($body);
    $res = _curl_request($formgettoken,$patoken);
    if($res == 'err'){
        return $res;
    }else{
        $tokenResult = array();
        $tokenResult =  json_decode($res, true);
        return $tokenResult;
    }
}

function _curl_request($url, $body, $header = array(), $method = "POST")
{
    array_push($header, 'Accept:application/json');
    array_push($header, 'Content-Type:application/json');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    switch ($method){
        case "GET" :
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST,true);
            break;
        case "PUT" :
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            break;
        case "DELETE":
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    if (isset($body{3}) > 0) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    if (count($header) > 0) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    $ret = curl_exec($ch);
    $err = curl_error($ch);
    $err = curl_getinfo($ch,CURLINFO_HTTP_CODE); //设置返回状态码
    curl_close($ch);
    if ($err == 200) {
        return $ret;
    }else{
        return 'err';
    }
}
//

//生成二维码
function createQrcode($uid){
        $info = M('Member')->where('uid='.$uid)->field('head_pic_id')->find();
        vendor("phpqrcode.phpqrcode");
        $data = 'http://'.$_SERVER['SERVER_NAME'].'/Mobile/Share/professionalDetail/?uid='.$uid.'&id='.$uid.'&type=1&target_id='.$uid.'&is_app=1';
        // 纠错级别：L、M、Q、H
        $level = 'H';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 10;
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        $path = $_SERVER['DOCUMENT_ROOT'].'/Uploads/qrcode/';
        $time = time();
        // 生成的文件名
        $fileName = $path.$uid.'_'.$level.'_'.$size.'_'.$time.'.png';
        \QRcode::png($data, $fileName, $level, $size);
        $head_pic_id = $info['head_pic_id'];
        if($head_pic_id){
            $logo = getImgUrl($head_pic_id);//需要显示在二维码中的Logo图像
        }else{
            $logo = $_SERVER['DOCUMENT_ROOT'].'/qrlogo.png';//需要显示在二维码中的Logo图像
        }
        
        $QR = $fileName;
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring ( file_get_contents ( $QR ) );
            $logo = imagecreatefromstring ( file_get_contents ( $logo ) );
            $QR_width = imagesx ( $QR );
            $QR_height = imagesy ( $QR );
            $logo_width = imagesx ( $logo );
            $logo_height = imagesy ( $logo );
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
        }
        imagepng ( $QR, $fileName );//带Logo二维码的文件名
        $qrcode = M('qrcode');
        $qrcodeinfo = $qrcode->where(array('uid'=>$uid))->find();
        $dataqrcode = array('level'=>$level,'uid'=>$uid,'size'=>$size,'update_time'=>$time);
        if($qrcodeinfo){
            $qrcode->where(array('uid'=>$uid))->save($dataqrcode);
        }else{
            $dataqrcode['create_time'] = time();
            $qrcode->add($dataqrcode); 
        }
        return 1;
}
//


//获取用户名
function getUserRealname($uid){
    $realname = D('Member')->where( array('uid'=>$uid) )->getField('realname');
    return $realname;
}
//


//获取用户类型
function getUserType($uid){
    $user_type = D('Member')->where( array('uid'=>$uid) )->getField('user_type');
    return $user_type;
}
//

//定时

//


