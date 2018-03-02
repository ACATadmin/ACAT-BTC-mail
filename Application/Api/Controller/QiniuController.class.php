<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/15
 * Time: 14:56
 */

namespace Api\Controller;
use Think\Upload\Driver\Qiniu\QiniuStorage;

class QiniuController extends ApiController
{
    public function _initialize()
    {
        $config = C("QINIU_CONFIG");
        $this->qiniu = new QiNiuStorage($config);
        parent:: _initialize();
    }

    /**
     * 获取七牛上传token
     */
    public function upLoadToken(){
        $this->outputJsonData($this->qiniu->getUploadToken());
    }
}