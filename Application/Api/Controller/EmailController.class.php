<?php
namespace Api\Controller;
use User\Api\UserApi;
/**
 * 测试（方法调用测试）
 * @author wei
 *
 */
class EmailController extends ApiController {
    
    public function getEmailGroup()
    {
        $list = C('EMAIL_GROUP');
        $this->outputJsonData( $list );
    }
    
    public function getEmailGroupMember()
    {
        $group = I('group');
        $model = M('Email');
        $map['status'] = 1;
        if($group)
        {
            $map['group'] = $group;
        }
        $list = $model->where($map)->select();
        $email = array_column($list,'email');
        $info['emails'] = arr2str($email); 
        $this->outputJsonData($info);
    }
    
}