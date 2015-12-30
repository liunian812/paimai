<?php
namespace Admin\Model;
use Think\Model;
class PagesModel extends Model {
    /* 自动验证 */
    protected $_validate = array();

    /* 自动完成 */
    protected $_auto = array();

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
