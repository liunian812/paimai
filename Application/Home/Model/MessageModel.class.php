<?php
namespace Index\Model;
use Think\Model;
class MessageModel extends Model {

    // protected $fields = array('id', 'title', 'email', 'tel', 'content', 'create_time', 'update_time', 'status', 'extend');

    /* 自动验证 */
    protected $_validate = array(
        array('title', 'require', '标题必填!'),
        array('email', 'email', '邮箱格式错误！'),
        array('tel', 'require', '联系方式必填！'),
        array('content', 'require', '内容必填！')
    );

    /* 自动完成 */
    protected $_auto = array(
        array('extend', 'callbackExtend', 3, 'callback'),
        array('create_time', NOW_TIME, 1),
        array('update_time', NOW_TIME, 2),
        array('status', 0)
    );

    public function callbackExtend($data) {
        if(empty($data)){
            return '';
        } else {
            // $data = array_filter($data);
            // if (empty($data)) {
            //     return '';
            // } else {
            //     return serialize($data);
            // }
            return serialize($data);
        }
    }

    public function input() {
        if(!$this->create()) {
            return false;
        } else {
            if($this->add()) {
                return true;
            } else {
                $this->error = '留言失败';
                return false;
            }
        }
    }

}
