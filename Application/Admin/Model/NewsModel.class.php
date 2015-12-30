<?php
namespace Admin\Model;
use Think\Model;
class NewsModel extends Model {
    /* 自动验证 */
    protected $_validate = array(
        array('catid','require','所属栏目必选！'),
        array('title','require','标题必填！'),
    );

    /* 自动完成 */
    protected $_auto = array(
        array('status', 1),
        array('create_time', 'callbackCreateTime', 3, 'callback'),
        array('update_time', 'time', 2, 'function'),
        array('extends', 'serialize', 3, 'function')
    );

    public function callbackCreateTime($data) {
        if(empty($data)){
            return NOW_TIME;
        } else {
            return strtotime($data);
        }
    }

    /* 插入操作 */
    public function input () {
        if (!$this->create()) {
            return false;
        } else {
            $this->add();
            return true;
        }
    }

    /* 更新操作 */
    public function update () {
        if (!$this->create()) {
            return false;
        } else {
            $this->save();
            return true;
        }
    }

}
