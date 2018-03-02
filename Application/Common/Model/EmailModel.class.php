<?php
namespace Common\Model;

/**
 * 公司模型
 */
class EmailModel extends BaseModel{

        /* 用户模型自动完成 */
        protected $_auto = array(
            array('create_time', NOW_TIME, self::MODEL_INSERT),
            array('update_time', NOW_TIME, self::MODEL_BOTH),
            array('status', 1, self::MODEL_INSERT),
            array( 'uid', 'is_login', self::MODEL_INSERT, 'function'),
        );

}
