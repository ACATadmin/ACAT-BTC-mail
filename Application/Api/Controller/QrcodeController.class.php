<?php
namespace Api\Controller;
use User\Api\UserApi;

/**
 * 二维码
 */
class QrcodeController extends ApiController {
	/*生成二维码*/
	public function createQrcode($uid=''){
		$this->check_parameter($uid,40051);
		$res = createQrcode($uid);

		$info = M('qrcode')->where(array('uid'=>$uid))->find();
		$path = C('HTTP_URL').'/Uploads/qrcode/';
		$url = $path.$info['uid'].'_'.$info['level'].'_'.$info['size'].'_'.$info['update_time'].'.png';

		$res = $this->apiDoNotice( $res,'生成二维码',array('url'=>$url));
		
		$this->outputJsonData( $res );

	}

	/*获取用户二维码*/
	public function getUserQrcode($uid=''){
		$path = C('HTTP_URL').'/Uploads/qrcode/';

		$info = M('qrcode')->where(array('uid'=>$uid))->find();
		if(!$info){
			createQrcode($uid);
			$info = M('qrcode')->where(array('uid'=>$uid))->find();
		}
                // 生成的文件名
                $res = $path.$info['uid'].'_'.$info['level'].'_'.$info['size'].'_'.$info['update_time'].'.png';

                $this->outputJsonData( $res );
	}

}