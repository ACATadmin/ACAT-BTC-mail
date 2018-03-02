<?php
namespace Api\Controller;
/**
 * 其他
 * @author 明亮
 *
 */
class OtherController extends ApiController {
	
	
	public function tt($type){
		echo $type;
	}
	
	public function versionCheck(){
		
		$arr = array(
			array(
					'resourceId'=>1,
					'resourceSign'=>'app_version',
					'resourceDescription'=>'app版本',
					'serverLevel'=>1.2,
					'upDate'=>date('Y-m-d'),
					'upDescription'=>'1.修复绝大多数bug，
2.修复第三方登录中存在的问题，
3.修复日期选择的问题
4.以及若干细节bug....',
					'resourceUrl'=>'http://hy.51tests.net/Uploads/Picture/2016-06-02/app-release.apk',
					'resourceSize'=>'5.7M',
			),
			array(
					'resourceId'=>2,
					'resourceSign'=>'app_version',
					'resourceDescription'=>'地区资源包版本',
					'serverLevel'=>11,
					'upDate'=>date('Y-m-d'),
					'upDescription'=>date('Y-m-d').'数据更新',
					'resourceUrl'=>'',
					'resourceSize'=>'300kb',
			)
		);
		
		$this->outputJsonData( $arr );
	}
	
	/**
	 * 根据分类type获取轮播列表
	 */
	public function getSlideList($type,$platform,$attribute_id=0,$special_id=""){
		$this->check_parameter($type,40011);

		if($GLOBALS['exitGetApiData'])return;
		
		$map['type']=$type;
		$map['platform']=$platform;
		if($attribute_id!=0){
		$map['attribute_id']=$attribute_id;
		}
		if($special_id){
			$map['special_id']=$special_id;
		}
		$res = D('Slide')->getList( $map,"order_sort desc" );
                
		$count = count($res);
		
		for( $i=0;$i<$count;$i++ ){
			if ($res[$i]['platform']!=1) {
					switch ($res[$i]['target_type']) {
						case '1':
						if ($res[$i]['target_id']) {
							$res[$i]['slide_url']="/Mobile/Content/contentDetail/content_id/".$res[$i]['target_id'].".html";
						}else{
							$res[$i]['slide_url']=$res[$i]['slide_url'];
						}
							
							break;
						case '2':
						if ($res[$i]['target_id']) {
							$res[$i]['slide_url']="/Mobile/Video/VideoDetail/content_id/".$res[$i]['target_id'].".html";
						}else{
							$res[$i]['slide_url']=$res[$i]['slide_url'];
						}
							
							break;
						case '3':
						if ($res[$i]['target_id']) {
							$res[$i]['slide_url']="/Mobile/Perform/details/perform_id/".$res[$i]['target_id'].".html";
						}else{
							$res[$i]['slide_url']=$res[$i]['slide_url'];
						}
							
							break;
						case '4':
						if ($res[$i]['target_id']) {
							$res[$i]['slide_url']="/Mobile/Trendsetter/details/id/".$res[$i]['target_id'].".html";
						}else{
							$res[$i]['slide_url']=$res[$i]['slide_url'];
						}
							
							break;
						case '5':
						if ($res[$i]['target_id']) {
							$res[$i]['slide_url']="/Mobile/Share/hongbao/id/".$res[$i]['target_id'].".html";
						}else{
							$res[$i]['slide_url']=$res[$i]['slide_url'];
						}
							
							break;
						
						default:
						
							break;
					}
			}

			
		}
		
		
		$this->outputJsonData( $res );
	}


	/**
	 * 根据唯一标识，获取文章配置详情
	 */
	public function getArticleDeploy($name){

		$this->check_parameter($name,43110);
		
		
		$res = D('ArticleDeploy')->where( array('name'=>$name) )->find();

		if(!$res) apiErrorCode(43111);
		$this->outputJsonData( $res );
	}

	/**
	 * 根据唯一标识，获取文章配置详情
	 */
	public function getConfigDetail(){
		$where=array('group'=>array('in','5'));
		$res = D('Config')->where($where)->field('name,value')->select();
		foreach($res as $key=>$value){
			$res[$key] = $value['value'];
			$ress[$key] = $value['name'];
		}
		$res = array_combine($ress, $res);
		if(!$res) apiErrorCode(43111);
		$this->outputJsonData( $res );
	}
	//生成反馈信息，并返回错误信息
	private function getFeedbackData($uid,$contents,$mobile){
		$uid 	= (int)$uid;
		$this->check_parameter($uid,40051);
		$this->check_parameter($contents,43001);
		$this->check_parameter($mobile,43002);
		
		$Feedback = D('Feedback');
		$data = $Feedback->create();
		if( !$data ){
			apiError($Feedback->getError(),40011);
		}
		return $data;
	}

	//添加意见反馈
	public function Feedback($uid,$contents,$mobile){
		$data = $this->getFeedbackData($uid,$contents,$mobile);
		
		$Feedback = D('Feedback');
		
		$ret = $Feedback->addFeedback($data);
		$res = $this->apiDoNotice($ret,'意见反馈添加');
		
		$this->outputJsonData( $res );
	}
	
	
	/* 上传文件 */
	public function uploadFile(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);
                        
		/* 记录附件信息 */
		if($info){
                        foreach($info as $k=>$v){
                            $return = $v;break;
			}
                        $return['path'] = get_file_path($return['id']);
                        $return['status'] = 1;
			//$return['data'] = think_encrypt(json_encode($info['download']));
		} else {
			$return['status'] = 0;
			$return['info']   = $File->getError();
		}

		/* 返回JSON数据 */
		$this->outputJsonData( $return );
	}
	
	//上传图片
	public function uploadPic(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
    
    	/* 调用文件上传组件上传文件 */
    	$Picture = D('Picture');
    	$pic_driver = C('PICTURE_UPLOAD_DRIVER');
    	$info = $Picture->upload(
    			$_FILES,
    			C('PICTURE_UPLOAD'),
    			C('PICTURE_UPLOAD_DRIVER'),
    			C("UPLOAD_{$pic_driver}_CONFIG")
    	); //TODO:上传到远程服务器
    
    
    
        
    	/* 记录图片信息 */
    	if($info){
    		foreach($info as $k=>$v){
				$return = $v;break;
			}
			$return['path'] = getImgUrl($return['id']);
			$return['status'] = 1;
    	} else {
    		$return['status'] = 0;
    		$return['info']   = $Picture->getError();
    	}
    	// echo json_encode($return);
    	$this->outputJsonData( $return );
	}
	
	
	//-------------------------------------------------------------------------------------------
	public function setTempVariable($tag='',$name='',$value=''){
		$ret = D('TempVariable')->setTempVariable($tag,$name,$value);
		$res = $this->apiDoNotice($ret,'临时变量存储');
		$this->outputJsonData( $res );
	}
	
	public function getTempVariable($tag='',$name=''){
		$res = D('TempVariable')->getTempVariable($tag,$name);
		$this->outputJsonData( $res );
	}
	
	
	/**
         * 根据城市名获取城市id
         * @return 
         */
        public  function getCityId($city_name){
            $this->check_parameter($city_name,400048);
            $map['region_name'] = array('like', '%'.$city_name.'%');
            $data=M("Region")->where($map)->find();
       
            $this->outputJsonData( $data['region_id'] );
            
        }


	     /**
         * 根据城市名获取城市id
         * @return 
         */
        public  function getMobileMenuList(){
       
            $data=$this->lists(D('MenuIcon'),null,"id desc");
       
            $this->outputJsonData( $data );
            
        }

        //留言板
        public function messageBoard($uid,$type,$contents){
            $this->check_parameter($uid,40051);
            $this->check_parameter($contents,60061);
            $data=D('MessageBoard')->create();
            $data['create_time']=time();
            $ret=D('MessageBoard')->add($data);
            $this->apiDoNotice($ret,'添加留言');
        }
        
        //广告启动页
        public function getStart($type){
            $list=D('AppStart')->where('type='.$type)->select();
            foreach($list as $k=>$v){
                $list[$k]['pic_url']=  getImgUrl($v['pic_id']);
            }
            $this->outputJsonData($list);
        }
	
}