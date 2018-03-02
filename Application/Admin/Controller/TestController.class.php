<?php
namespace Admin\Controller;
use Think\Upload\Driver\Qiniu\QiniuStorage;
/**
 * 
 * @author 行者
 *
 */
class TestController extends AdminController {
    
    public function _initialize()
    {
        $config = C("QINIU_CONFIG");
        $this->qiniu = new QiNiuStorage($config);
        parent:: _initialize();
    }
    
    
    //七牛
    public function qiniuUpload(){
        $this->display('qiniuupload');
    }
    
    //上传单个文件 用uploadify
    public function uploadOne()
    {
        set_time_limit(0);
        
        $file = $_FILES['qiniu_file'];
        $file = array(
            'name' => 'file',
            'fileName' => 'lesson_video_'.NOW_TIME.rand(0,9).'.mp4',
            'fileBody' => file_get_contents($file['tmp_name'])
        );
        
        $config = array();
//        $config['fsizeMin'] = '1';
//        $config['fsizeLimit'] = '111111111111111111';
        $result = $this->qiniu->upload($config, $file);
        if (!$result) {
            $result = array(
                'error' => $this->qiniu->error,
                'errorStr' => $this->qiniu->errorStr
            );
        } else {
            $result['error'] = 0;
        }
        
        $this->ajaxReturn($result);
    }
    
    //获取文件路径
    public function getFileUrl(){
        $key=I('key');
        $url=D('Video')->getQiniuUrl($key);
        $this->ajaxReturn($url);
    }
    //
}