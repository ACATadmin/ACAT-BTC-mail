<?php

namespace Addons\VideoJs;
use Common\Controller\Addon;

/**
 * VideoJs视频播放器插件
 * @author 行者
 */

    class VideoJsAddon extends Addon{

        public $info = array(
            'name'=>'VideoJs',
            'title'=>'VideoJs视频播放器',
            'description'=>'基于VideoJs实现的Html5视频播放器',
            'status'=>1,
            'author'=>'行者',
            'version'=>'0.1'
        );

        public function install(){
        	       	
        	/* 先判断插件需要的钩子是否存在 */
        	$this->getisHook('VideoJs', $this->info['name'], $this->info['description']);
        	
            return true;
        }

        public function uninstall(){
        	
        	/*删除插件钩子*/
        	$hook_mod = M('Hooks');
        	$where['name'] = $this->info['name'];
        	$hook_mod->where($where)->delete();
        	
            return true;
        }
        
        //获取插件所需的钩子是否存在
        public function getisHook($str, $addons, $msg=''){
        	$hook_mod = M('Hooks');
        	$where['name'] = $str;
        	$gethook = $hook_mod->where($where)->find();
        	if(!$gethook || empty($gethook) || !is_array($gethook)){
        		$data['name'] = $str;
        		$data['description'] = $msg;
        		$data['type'] = 1;
        		$data['update_time'] = NOW_TIME;
        		$data['addons'] = $addons;
        		if( false !== $hook_mod->create($data) ){
        			$hook_mod->add();
        		}
        	}
        }
        
        
        public function getVideoConfig( $param ){
        	$config = $this->getConfig();
        	
        	//播放器封面
        	if( isset($param['cover_id']) ){				//指定视频封面
        		$config['cover'] = get_cover( $param['cover_id'] ,'path');
        	}elseif( $config['use_zdy_video_cover']==0 ){	//后台默认选择封面
        		$config['cover'] = '/Addons/VideoJs/Static/images/'.$config['video_cover'];
        	}else{											//后台自定义默认封面
        		$config['cover'] = get_cover( $config['zdy_video_cover'] ,'path');
        	}
        	        	
        	//个性化配置
        	if( is_array($param) ){
        		$config['width'] 	= isset($param['width'])?$param['width']:$config['width'];
        		$config['height'] 	= isset($param['height'])?$param['height']:$config['height'];
        	}
        	
        	if( $param['auto'] ){
        		//$config['width'] = $config['height'] = '';
        	}
        	
        		
        	//依据视频大小生成播放器开始按钮距离左右的位置
        	$config['top'] 	= $config['height']/2-42;
        	$config['left'] = $config['width']/2-42;

        	return $config;
        }

        //实现的VideoJs钩子方法
        public function VideoJs($param){
            
			//视频路径
        	$video_path = '';
        	
        	//播放器默认配置
        	$config = $this->getVideoConfig( $param );

            if($param['use_thirdparty_video']==1){
                $data['video_path'] = $param['thirdparty_url'];
                $this->assign('data',$data);
                $this->display('View/iframe');
            }else{
	        	//获取资源与配置信息
	        	if( !is_array($param) && is_numeric($param) ){			//如果传入参数为视频资源ID
	        		$file_id = $param;
	        	}elseif( is_array($param) && isset($param['file_id'])){
	        		$file_id = $param['file_id']; 
	        	}elseif( isset($param['url']) ){						//
	        		
	        	}else{
	        		return '';
	        	}        	
	
	        	//视频信息
	        	$vInfo = D('File')->where( array('id'=>$file_id) )->field('*')->find();
	
	        	$video_path = C('DOWNLOAD_UPLOAD.rootPath').$vInfo['savepath'].$vInfo['savename'];	//'http://'.$_SERVER['HTTP_HOST'].
	        	$data['video_path'] = str_replace('./', '/', $video_path);
	        	
	            //显示
	            $this->assign('data',$data);
	            $this->assign('config',$config);
	            $this->display('View/player');
            }
        	
        }

    }