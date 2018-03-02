<?php
namespace Common\Model;

class SmsLogModel extends BaseModel{

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('client_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1)
    );
    
    public function getInfo($map, $field = true) {
        return parent::getInfo($map, $field);
    }
    
    //生成短信验证码
    public function createCode($length = 6) {
        return randCode($length,1);
    }

    /**
     * sendMessageWithoutCheckSelf  发送短信消息，不屏蔽自己
     * @param $to_uids 接收消息的用户们
     * @param string $title 消息标题
     * @param string $content 消息内容
     * @param string $url 消息指向的路径，U函数的第一个参数
     * @param array $url_args 消息链接的参数，U函数的第二个参数
     * @param int $from_uid 发送消息的用户
     * @param int $type 消息类型，0系统，1用户，2应用
     * @return bool
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function sendMessagesms($to_uids, $title = '您有新的消息', $content = '', $url = '', $url_args = array(), $sms_type, $from_uid = -1)
    {
        $from_uid == -1 && $from_uid = is_login();
        // $message_content_id = $this->addMessageContent($from_uid, $title, $content, $url, $url_args, $type);
        $to_uids = is_array($to_uids) ? $to_uids : array($to_uids);

        foreach ($to_uids as $to_uid) {


            $message['mobile'] = $to_uid['mobile'];
            $message['title'] = $title;
            $message['sms_type'] = $sms_type;
            $message['from_uid'] = $from_uid;
            $message['content']='恭喜您成为1号票仓的'.$to_uid['nickname'].'卖家，快去挂票吧，一大波订单正等你来，别耽误啦！';
            $ret=sendSms($message['mobile'],$message['content']);
            $message['is_success'] = $ret;
            $this->addMessageContent($message);
            unset($message);
        }
        return true;
    }

    /**
     * addMessageContent  添加消息内容到表
     * @param $from_uid 发送消息的用户
     * @param $title 消息的标题
     * @param $content 消息内容
     * @param $url 消息指向的路径，U函数的第一个参数
     * @param $url_args 消息链接的参数，U函数的第二个参数
     * @param $type 消息类型，0系统，1用户，2应用
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    private function addMessageContent($message)
    {
        // vde($message);

        $data_content['sms_type'] = $message['sms_type'] ;
        $data_content['from_id'] = $message['from_uid'];
        $data_content['mobile'] = $message['mobile'];
        $data_content['title'] = $message['title'];
        $data_content['create_time'] = time();
        $data_content['client_ip'] = get_client_ip();
        $data_content['content'] = $message['content'];
        $data_content['is_success'] = $message['is_success'];
         // vde($data_content);
        $this->add($data_content);
        // return $message_id;
    }


//    /**
//     * sendMessageWithoutCheckSelf  发货短信
//     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
//     */
//    public function sendMessagesmsToGoods($date)
//    {
//        $date['sms_type'] = 2;
//        $date['content']="亲，您在1号票仓购买的".$date['title']."门票".$date['buy_num']."张，卖家已经发货了，快递单号为".$date['shipping_no']."，请您耐心等候哦。";
//        $ret=sendSmsNo($date['mobile'],$date['content']);
//        $date['is_success'] = $ret;
//        $this->MessagesmsToGoods($date);
//        return true;
//    }
    
    /**
     * sendMessageWithoutCheckSelf  发货短信
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function sendMessagesmsToGoods($date)
    {
        $date['sms_type'] = 2;
        //$date['content']="亲，您在善品汇购买的".$date['order_sn']."订单商品".$date['buy_num']."件，卖家已经发货了，快递单号为".$date['shipping_no']."，请您耐心等候哦。";
        $date['content']="亲，您在善品汇购买的".$date['order_sn']."订单商品".$date['buy_num']."件，卖家已经发货了";
        $ret=sendSmsNo($date['mobile'],$date['content']);
        $date['is_success'] = $ret;
        $this->MessagesmsToGoods($date);
        return true;
    }

    /**
     * MessagesmsToGoods  添加消息内容到表
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    private function MessagesmsToGoods($date)
    {
        $date['create_time'] = time();
        $date['client_ip'] = get_client_ip();
        $this->add($date);
        // return $message_id;
    }
	
}