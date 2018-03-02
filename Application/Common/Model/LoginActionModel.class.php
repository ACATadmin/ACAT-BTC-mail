<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20
 * Time: 11:23
 */
namespace Common\Model;

/**
 * 用户登录行为模型类
 * Class LoginActionModel
 * @package Common\Model
 */
class LoginActionModel extends BaseModel
{
    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array( 'login_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1 ),
        array( 'create_time', NOW_TIME, self::MODEL_INSERT ),
        array( 'login_time', NOW_TIME, self::MODEL_INSERT ),
        array( 'update_time', NOW_TIME, self::MODEL_BOTH ),
        array( 'status', 1, self::MODEL_INSERT ),
    );

    /**
     * 用户登录行为
     * @param $username
     * @param $password
     * @return int
     */
    public function action( $username, $password )
    {
        // 执行用户表数据的查询
        $map['username'] = $username;

        /* 获取用户数据 */
        $user = M( 'UcenterMember' )->where( $map )->find();
        if ( is_array( $user ) && is_administrator( $user['id'] ) ) {
            return 1;
        }
        if ( is_array( $user ) && $user['status'] ) {
            /* 验证用户密码 */
            $encryptPwd = think_ucenter_md5( $password, UC_AUTH_KEY );
            if ( $encryptPwd === $user['password'] ) {
                $correct      = 1;
                $recordStatus = 1;
                $this->addAction( $username, $password, $encryptPwd, $user, $correct, $recordStatus );
            } else {
                // 密码错误
                $correct      = 0;
                $recordStatus = 2;
                $this->addAction( $username, $password, $encryptPwd, $user, $correct, $recordStatus );
            }

            return $this->getLoginErrorNumbers( $username );
        }
    }

    /**
     * 用户登录行为数据添加动作
     * @param $username
     * @param $password
     * @param $encryptPwd
     * @param $user
     * @param $correct
     * @param $recordStatus
     */
    protected function addAction( $username, $password, $encryptPwd, $user, $correct, $recordStatus )
    {
        // 添加用户登录信息
        $info = array(
            'username'      => $username,
            'original_pwd'  => $password,
            'encrypt_pwd'   => $encryptPwd,
            'uid'           => $user['id'],
            'is_correct'    => $correct,
            'record_status' => $recordStatus,
        );
        $data = $this->create( $info );
        if ( $data ) {
            $this->add( $data );
        }
    }

    /**
     * 查询已经登录的错误次数
     * @param $username
     * @return int
     */
    protected function getLoginErrorNumbers( $username )
    {
        // 查询当前用户已经登录的次数
        $start = strtotime( date( 'Y-m-d', NOW_TIME ) );
        $end   = $start + 86399;
        $where = array(
            'username'   => $username,
            'is_correct' => 0,
            'login_time' => array( 'between', array( $start, $end ) ),
        );
        $login = $this->where( $where )->select();

        return count( $login );
    }
}