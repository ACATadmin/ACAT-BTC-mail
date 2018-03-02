<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
$CONFIG = array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Admin',
    'DEFAULT_CONTROLLER' => 'Index',
    'DEFAULT_ACTION'     => 'index',
    'MODULE_DENY_LIST'   => array('Common','User','Install'),
//     'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'vP]U6sFVh?xre2}D1qAp:-g{|&+XJOnd8G$MLK^Q', //默认数据加密KEY

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 2, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数
    /* 加载函数库 */
    'LOAD_EXT_FILE' 	=> 'common,order,repayment',

    /* 加载按钮权限的扩展 */
//    'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Extend',

    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'alphamail', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'bl_', // 数据库表前缀
    'DB_CHARSET'=> 'utf8mb4',

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
    
    
    //---------------------------------------------------
    
    //短信
    
    //短信变量替换
    'SMS_TPL_REPLACE'   => array(
                        'var_code'    => '123456',
    ),
    
    //短信模板
    'SMS_TPL'   => array(
                'verification_code'    => '您的验证码是：var_code',
    ),
    
    //短信验证码过期时间 秒
    'SMS_CODE_PAST' =>  300,
    
    //短信轰炸检测时间范围 秒
    'SMS_CODE_PREVENT_TIME' =>  86400,
    
    //短信轰炸检测数量
    'SMS_CODE_PREVENT_NUM' =>  400,    
    
    
    //---------------------------------------------------


    //-----------------------------------------------------接口
    'DEVELOPER'	=> array(
    		array('id'=>'0','name'=>'未分配'),
    		array('id'=>'1','name'=>'海涛'),
    ),
    'DEVELOPMENT_STATUS' =>array(
    		array('id'=>'0','name'=>'未开始'),
    		array('id'=>'1','name'=>'进行中'),
    		array('id'=>'2','name'=>'完成调试中'),
    		array('id'=>'3','name'=>'已完成'),
    ),
    
    //接口统一请求字段
    'API_COM_REQUEST_FIELD'=>array(
    		array(
    				'request_field_name'	=> 'appid',
    				'must_field'			=> 0,
    				'request_field_desc'	=> "请求客户端ID【公共参数】",
    		),
    		array(
    				'request_field_name'	=> 'request_time',
    				'must_field'			=> 0,
    				'request_field_desc'	=> "请求时间戳【公共参数】",
    		),
    		array(
    				'request_field_name'	=> 'access_token',
    				'must_field'			=> 0,
    				'request_field_desc'	=> "调用接口凭证（调试期间暂时不考虑）【公共参数】",
    		),
    		/*
    		array(
    				'request_field_name'	=> 'user_token',
    				'must_field'			=> 0,
    				'request_field_desc'	=> "用户唯一标识，参照返回说明【公共参数】",
    		),
    		*/
    ),
		// 接口统一返回字段
		'API_COM_RETURN_FIELD' => array (
				array (
						'return_field_name' => 'user_token',
						'must_field' => 0,
						'return_field_desc' => "用户唯一标识。接口返回（请求中带入则使用接口中的值，无则由服务器端生成一个不重复的32位编码标识），用户登录后会与用户关联，相关操作会验证此值与当前登录用户的uid的匹配性。【公共参数】" 
				) 
		),
		
		// 分页接口附加返回字段
		'PAGINATION_FIELD' => array (
				array (
						'request_field_name' => 'p',
						'must_field' => 0,
						'request_field_desc' => "当前访问页数【公共参数】" 
				),
				array (
						'request_field_name' => 'page_size',
						'must_field' => 0,
						'request_field_desc' => "每页数据条数【公共参数】" 
				) 
		),    		
    
                'YES_NO' => array(0 => '否', 1 => '是'),
    
                'APPID'			=> 'wx65e35682f9810d46',
                'APPSECRET'		=> '7adb462a18862265de43692cd069cfd6',
                'PAYMENT_PATH'	=> './Application/Common/Payment',
    
                //支付回调地址
                'NOTIFY_URL'	=>	'http://'.$_SERVER['HTTP_HOST'].'/Mobile/Order/notify',			//服务器通知地址
                'RETURN_URL'	=>	'http://'.$_SERVER['HTTP_HOST'].'/Mobile/Order/callback',		//用户浏览器回调页面
    
                'DELETE_STATUS' => -1,
    
                'USER_STATUS'   => array(
                        0 => array('id'=>0,'name'=>'禁用'),
                        1 => array('id'=>1,'name'=>'正常'),
                ),

                
    
               
    
                'PriceFuHao'=>'¥',
    
                'ACCOUNT_CHANGE_TYPE'   => array(
                        0 => array('id'=>0,'name'=>'充值'),
                        1 => array('id'=>1,'name'=>'提现'),
                        2 => array('id'=>2,'name'=>'赞赏收入'),
                        97 => array('id'=>97,'name'=>'余额支付（未指定特定类型）'),
                        98 => array('id'=>98,'name'=>'管理员调节'),
                        99 => array('id'=>99,'name'=>'其他'),
                        3 => array('id'=>3,'name'=>'赞赏支出'),
                        4 => array('id'=>4,'name'=>'问询收入'),
                        5 => array('id'=>5,'name'=>'问询支出'),
                        6 => array('id'=>6,'name'=>'课程支出'),
                        7 => array('id'=>7,'name'=>'提现失败金额回退'),
                ),
    
                'ACCOUNT_CHANGE_STATUS' => array(
                        1 => array('id'=>1,'name'=>'成功'),
                        2 => array('id'=>2,'name'=>'处理中'),
                        3 => array('id'=>3,'name'=>'失败'),
                ),
    
                'CARDS_TYPE' => array(
                        1 => array('id'=>1,'name'=>'支付宝','code'=>'alipay'),
                        2 => array('id'=>2,'name'=>'微信','code'=>'wxpay'),
                        3 => array('id'=>3,'name'=>'银行卡','code'=>'bank'),
                ),
    
               
    
                'PAY_CODE' => array(
                        'alipay' => array('id'=>'alipay','name'=>'支付宝'),
                        'wxpay' => array('id'=>'wxpay','name'=>'微信'),
                        'balance' => array('id'=>'balance','name'=>'余额支付'),
                ),
    
                //快递100
//                'SHIPPING_CUSTOMER' => '246CAD580CB334CA55F65E3F0DB2E83A',
//                'SHIPPING_KEY'      => 'QcifnCAt3624',
                //----
    
                //快递100
                'SHIPPING_CUSTOMER' => '1374FE2CF6B08AE4942738310765A906',
                'SHIPPING_KEY'      => 'kWaTFPtE2534',
                //----
    
                'SIGN_COLOR'    => array('#f3d353','#09c2ce','#f56f31','#9535f5','#f43451'),
    
                'DAY'   =>3600*24,
    
                'FEED_BACK_TYPE' => array(
                    1 => array('id'=>1,'name'=>'软件功能'),
                    2 => array('id'=>2,'name'=>'投诉与建议'),
                    3 => array('id'=>3,'name'=>'其他'),
                ),
    
                'PRODUCE_STATUS' => array(
                    0 => array('id'=>0,'name'=>'未生产'),
                    1 => array('id'=>1,'name'=>'生产中'),
                    2 => array('id'=>2,'name'=>'生产完成'),
                ),
    
                'SLIDE_TARGET_TYPE' => array(
                    0 => array('id'=>0,'name'=>'不跳转'),
                    1 => array('id'=>1,'name'=>'直播预告'),
                    2 => array('id'=>2,'name'=>'视频'),
                    3 => array('id'=>3,'name'=>'资讯'),
                    4 => array('id'=>4,'name'=>'链接跳转'),
                ),
                'PLATE_TYPE' => array(
                    1 => array('id'=>1,'name'=>'预告'),
                    2 => array('id'=>2,'name'=>'推荐'),
                    3 => array('id'=>3,'name'=>'热门'),
                    4 => array('id'=>4,'name'=>'分类'),
                    5 => array('id'=>5,'name'=>'视频首页'),
                    6 => array('id'=>6,'name'=>'资讯首页'),
                    7 => array('id'=>7,'name'=>'启动广告页'),
                ),
                'SEND_ONE_HUNDRED'=> array(
                    0 => array('id'=>0,'name'=>'在途'),
                    1 => array('id'=>1,'name'=>'揽件'),
                    2 => array('id'=>2,'name'=>'疑难'),
                    3 => array('id'=>3,'name'=>'签收'),
                    4 => array('id'=>4,'name'=>'退签'),
                    5 => array('id'=>5,'name'=>'派件'),
                    6 => array('id'=>6,'name'=>'退回'),
                ),
    
                
                
    
                
    
                 'PUBLIC_SELECT' => array(
                     0 => array('id'=>0,'name'=>'否'),
                     1 => array('id'=>1,'name'=>'是'),
                 ),
    
                 'PUBLIC_STATUS' => array(
                     0 => array('id'=>0,'name'=>'禁用'),
                     1 => array('id'=>1,'name'=>'启用'),
                 ),
    
                 'PUBLIC_STATUS_TWO' => array(
                     0 => array('id'=>0,'name'=>'停用'),
                     1 => array('id'=>1,'name'=>'启用'),
                 ),
    
                 'DEPOSIT_PAY_STATUS' => array(
                     0 => array('id'=>0,'name'=>'未缴'),
                     1 => array('id'=>1,'name'=>'缴纳中'),
                     2 => array('id'=>2,'name'=>'已缴'),
                 ),
                 
    
                 'WEEK_DAY' => array(
                     1 => array('id'=>1,'name'=>'周一'),
                     2 => array('id'=>2,'name'=>'周二'),
                     3 => array('id'=>3,'name'=>'周三'),
                     4 => array('id'=>4,'name'=>'周四'),
                     5 => array('id'=>5,'name'=>'周五'),
                     6 => array('id'=>6,'name'=>'周六'),
                     7 => array('id'=>7,'name'=>'周日'),
                 ),
    
                 
    
                'WITHDRAW_STATUS_LOG'   => array(
                    1 => array('id'=>1,'name'=>'提现成功'),
                    2 => array('id'=>2,'name'=>'等待审核'),
                    3 => array('id'=>3,'name'=>'提现失败'),
                ),

                'WITHDRAW_STATUS'   => array(
                    0 => array('id'=>0,'name'=>'等待审核'),
                    1 => array('id'=>1,'name'=>'提现成功'),
                    2 => array('id'=>2,'name'=>'提现失败'),
                ),
    
                
//                'CYCLE' => array(
//                    1 => array('id'=>1,'name'=>'3个月'),
//                    2 => array('id'=>2,'name'=>'4个月'),
//                    3 => array('id'=>3,'name'=>'N个月'),
//                    4 => array('id'=>4,'name'=>'随借随还（不大于3个月）'),
//                ),
    
                'CYCLE' => array(
                    1 => array('id'=>1,'name'=>'1个月'),
                    2 => array('id'=>2,'name'=>'2个月'),
                    3 => array('id'=>3,'name'=>'3个月'),
                    4 => array('id'=>4,'name'=>'4个月'),
                    5 => array('id'=>5,'name'=>'5个月'),
                    6 => array('id'=>6,'name'=>'6个月'),
                    7 => array('id'=>7,'name'=>'7个月'),
                    8 => array('id'=>8,'name'=>'8个月'),
                    9 => array('id'=>9,'name'=>'9个月'),
                    10 => array('id'=>10,'name'=>'10个月'),
                    11 => array('id'=>11,'name'=>'11个月'),
                    12 => array('id'=>12,'name'=>'12个月'),
                ),
    
    
                'ACCRUED_INTEREST_WAY' => array(
                    1 => array('id'=>1,'name'=>'按日计息'),
//                    2 => array('id'=>2,'name'=>'按月计息'),
                ),
    
                'USER_TYPE' => array(
                    1 => array('id'=>1,'name'=>'核心企业'),
                    2 => array('id'=>2,'name'=>'关联企业'),
                    3 => array('id'=>3,'name'=>'保理公司'),
                ),

                'AIM_TYPE' => array(
                    1 => array('id'=>1,'name'=>'核心企业'),
                    2 => array('id'=>2,'name'=>'关联企业'),
                ),

                'RELATION_TYPE' => array(
                    1 => array('id'=>1,'name'=>'下游'),
                    //2 => array('id'=>2,'name'=>'上游'),
                ),
    
                'PAY_OBJECT' => array(
                    1 => array('id'=>1,'name'=>'核心企业'),
                    2 => array('id'=>2,'name'=>'下游企业'),
                ),
    
                'REPAYMENT_WAY_CODE' => array(
                    1 => array('id'=>1,'name'=>'XXHB'),
                    2 => array('id'=>2,'name'=>'YCXBX'),
                ),
    
                'ORDER_TYPE' => array(
                    1 => array('id'=>1,'name'=>'正常'),
                    2 => array('id'=>2,'name'=>'展期'),
                ),
    
                
                'ANDROID_APP_DOWNLOAD_LINK' => 'https://www.pgyer.com/AB3g',
                'IOS_APP_DOWNLOAD_LINK' => 'https://www.pgyer.com/hYyU',
    
                 //七牛配置项
                'QINIU_CONFIG'=> array(
                    'accessKey'=>'68F9h7Ybwp-5mcbCSUUeYoDY9l2pNnjQVNYOmzua',
                    'secrectKey'=>'qBJ891XhYi6YrJ_5LG46veSHKgCdSwDNuHBTlR8x',
                    'bucket'=>'yishengyiyi',
                    'domain'=>'ovr7ldzgg.bkt.clouddn.com'
                ),
    
                //环信
                //环信Client Id
                'HX_CLIENT_ID' => 'YXA6g61ukJ0IEeeLhPvxYtKHCg',
                //环信Client Secret
                'HX_CLIENT_SECRET' => 'YXA61RYC1za_5YrhRNY7kMo-VIjOksQ',
                //环信TOKEN获取地址
                'HX_TOKEN_URL' => 'https://a1.easemob.com/1157170919178330/yishengyiyi/',
    
                
    
                //极光
                'APP_KEY' => '282ea5b27d6e1b267ae39135',
                'MASTER_SECRET' => 'd3fab2ff97267a7cb264389a',
    
    
                'APP_AUTH'  => array(
                        '10001' => array(
                                'appid'     => '10001',
                                'appkey'    => 'Y9FIGZYSYY',
                                'desc'      => '安卓客户端',

                        ),
                        '10002' => array(
                                'appid'     => '10002',
                                'appkey'    => 'C1XGPFYSYY',
                                'desc'      => 'IOS客户端',
                        ),
                ),
    
                'HTTP_URL'=>'http://'.$_SERVER['HTTP_HOST'],
    
                'URL_HOST' => 'http://www.doc199.com',
    
                
                
);

//错误码
$errcode = require_once 'errcode.php';
foreach($errcode as $key=>$val){
	$CONFIG[$key] = $val;
}
//还款公式配置
$errcode = require_once 'repayment.php';
foreach($errcode as $key=>$val){
	$CONFIG[$key] = $val;
}
return $CONFIG;