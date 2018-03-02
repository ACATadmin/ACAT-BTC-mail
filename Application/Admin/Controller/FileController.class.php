<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends AdminController {

    /* 文件上传 */
    public function upload(){
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
            $return['data'] = think_encrypt(json_encode($info['download']));
            $return['info'] = $info['download']['name'];
            $return['id'] = $info['download']['id'];
            $return['name'] =$info['file']['name'];
            $return['fid']=$info['file']['id'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }
	
    
    
    
    
    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPictureSquare(){
    	
    	
    	//echo json_encode($_FILES);exit;
    	
    	$this->square = 1;
    	return $this->uploadPicture();
    }
    
    public function uploadPicture(){
    	//TODO: 用户登录检测
    	/* 返回标准数据 */
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
    		$return['status'] = 1;
    		$return = array_merge($info['download'], $return);
    	} else {
    		$return['status'] = 0;
    		$return['info']   = $Picture->getError();
    	}
    
    	// if( $this->square ){
    	// 	$image = new \Think\Image();
    	// 	$image->open(  '.'.$return['path'] );// 生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
    	// 	$image->thumb(500, 500,\Think\Image::IMAGE_THUMB_CENTER)->save(  '.'.$return['path'] );
    	// }
    
    
    	/* 返回JSON数据 */
    	$this->ajaxReturn($return);
    }
    
    
    
    
    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture2222222(){
        //TODO: 用户登录检测

        /* 返回标准数据 */
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
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


    public function uploadPic(){
        //vde($_FILES);
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
                $return = $v;
                break;
            }
            if($return['status']!=1){
                $return['status'] = 1;
            }
            // $data = array('head_pic_id'=>$return['id']);
            // D('Member')->where('uid='.session('user_auth.uid'))->save($data);
            // $return['path']="/Uploads/Picture/".$return['savepath']."zoom/meddle_".$return['savename'];
            // $return['path']=$return['path']."?".rand(1000,9999);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }
        $this->ajaxReturn($return);
    }

    //裁剪图像
     public function updateHeader(){
         
         $path = ".".I("uploaded_image");
     
         ///解析crop参数
         $crop   = $_REQUEST['crop'];
         $crop   = explode(',', $crop);
         $x      = $crop[0];
         $y      = $crop[1];
         $width  = $crop[2];
         $height = $crop[3];
     
         $imageSize=getimagesize( $path );
     
         $image = new \Think\Image();
         $image->open( $path );
     
         //生成将单位换算成为像素
         $x      = $x * $imageSize[0];
         $y      = $y * $imageSize[1];
         $width  = $width * $imageSize[0];
         $height = $height * $imageSize[1];
     
         //裁剪图像并保存
         $save_path = $path; //$path
         $image->crop(  $width, $height, $x, $y)->save( $save_path );
     
         //头像压缩
         $image->thumb(240, 240)->save( $save_path );

         $paths=I("uploaded_image")."?".rand(1000,9999);
         $res=array('statues' =>1 ,"paths"=>$paths);
         $this->ajaxReturn($res);
     }

    //裁剪图像
    public function testupdateHeader(){
        
        // $paths = ".".I("uploaded_image");
        $path=".".get_cover_zoom(I("imgid"),"path");
        // vde(array($paths, $pathes));
    
        ///解析crop参数
        $crop   = $_REQUEST['crop'];
        $crop   = explode(',', $crop);
        $x      = $crop[0];
        $y      = $crop[1];
        $width  = $crop[2];
        $height = $crop[3];
    
        $imageSize=getimagesize( $path );
    
        $image = new \Think\Image();
        $image->open( $path );
    
        //生成将单位换算成为像素
        $x      = $x * $imageSize[0];
        $y      = $y * $imageSize[1];
        $width  = $width * $imageSize[0];
        $height = $height * $imageSize[1];
    
        //裁剪图像并保存
        $save_path = $path; //$path
        $image->crop(  $width, $height, $x, $y)->save( $save_path );
    
        //头像压缩
        // $image->thumb(240, 240)->save( $save_path );

        $paths=get_cover_zoom(I("imgid"),"path")."?".rand(1000,9999);
        $res=array('statues' =>1 ,"paths"=>$paths);
        $this->ajaxReturn($res);
    }

    //裁剪图像
    public function updateHeaderes(){
        
        // $paths = ".".I("uploaded_image");
        $path=".".get_cover_zoom(I("imgid"),"path");
       
    
        ///解析crop参数
        // $crop   = $_REQUEST['crop'];
        // $crop   = explode(',', $crop);
        $x      = I('x');
        $y      = I('y');
        $width  = I('w');
        $height = I('h');
     // vde($x);
        $imageSize=getimagesize( $path );
    
        $image = new \Think\Image();
        $image->open( $path );
    
        //生成将单位换算成为像素
        $x      = $x * $imageSize[0];
        $y      = $y * $imageSize[1];
        $width  = $width * $imageSize[0];
        $height = $height * $imageSize[1];
    
        //裁剪图像并保存
        $save_path = $path; //$path
        $image->crop(  $width, $height, $x, $y)->save( $save_path );
    
        //头像压缩
        // $image->thumb(240, 240)->save( $save_path );

        $paths=get_cover_zoom(I("imgid"),"path")."?".rand(1000,9999);
        $res=array('statues' =>1 ,"paths"=>$paths);
        // $this->ajaxReturn($res);
        echo json_encode($res);
    }


    public function uploadPices(){
        //vde($_FILES);
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
                $return = $v;
                break;
            }
            if($return['status']!=1){
                $return['status'] = 1;
            }
            //验证尺寸
            $path=".".$return['path'];
            $xd=getimagesize ( $path );
            $yd=explode("*", I('suggestSize'));
            $w=intval($yd[0]);
            $h=intval($yd[1]);
            if ($xd[0]<$w) {
            $return['status'] = 0;
            $return['info']   = "上传的图片最小宽*高不符合要求，请重新上传";
            }
            if ($xd[1]<$h) {
            $return['status'] = 0;
            $return['info']   = "上传的图片最小宽*高不符合要求，请重新上传";
            }
            // $data = array('head_pic_id'=>$return['id']);
            // D('Member')->where('uid='.session('user_auth.uid'))->save($data);
            // $return['path']="/Uploads/Picture/".$return['savepath']."zoom/meddle_".$return['savename'];
            // $return['path']=$return['path']."?".rand(1000,9999);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }
        // $this->ajaxReturn($return);

        echo json_encode($return);
    }

}
