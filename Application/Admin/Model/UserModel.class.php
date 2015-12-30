<?php
namespace Admin\Model;
use Think\Model;
class UserModel extends Model {
    /* 自动验证 */
    protected $_validate = array(
        array('user_name','require','用户名必选！')
    );

    /* 自动完成 */
    protected $_auto = array(
        array('status', 1),
        array('create_time', 'time', 3, 'function'),
        array('update_time', 'time', 2, 'function')
    );

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
