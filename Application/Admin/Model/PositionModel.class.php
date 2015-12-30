<?php
namespace Admin\Model;
use Think\Model;
class PositionModel extends Model {
    /* 自动验证 */
    protected $_validate = array(
        array('type', 'require', '栏目栏目必填'),
        array('name', 'require', '推荐位名称必填'),
    );

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

    public function getAll($map = array(), $order = '', $field = true) {
        return $this->where($map)->order($order)->field($field)->select();
    }

}
