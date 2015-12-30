<?php
namespace Admin\Model;
use Think\Model;
class BannerModel extends Model {
    /* 自动验证 */
    protected $_validate = array(
        array('name','require','栏目名称必填！'),
        array('type','require','栏目类型必填！')
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

}
