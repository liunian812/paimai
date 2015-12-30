<?php
namespace Admin\Model;
use Think\Model;
class MenuModel extends Model {

    private $_formatTree = array();

    //修改表名称
    protected $tableName = 'auth_rule';

    /* 自动验证 */
    protected $_validate = array(
        array('title','require','规则名称必填！'),
        array('name','require','规则标识必填！'),
        //array('name','','规则标识符已经存在！', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT)
    );

    /* 自动完成 */
    protected $_auto = array(
        array('status', '1', self::MODEL_BOTH),
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

    public function getAll($map = array(), $order = '', $field = true) {
        $cates = $this->where($map)->field($field)->order($order)->select();
        $datas = $tree =  array();
        foreach ($cates as $key => $val) {
            $datas[$val['id']] = $val;
        }
        return $datas;
    }

    /* 获取栏目tree操作 */
    public function lists ($map = array()) {
        $rules = $this->getAll($map, 'sort desc, id asc');
        $datas = $tree =  array();
        foreach ($rules as $key => $val) {
            $datas[$val['id']] = $val;
        }
        foreach ($datas as $key => $val) {
            if(isset($datas[$val['pid']])){
                $datas[$val['pid']]['child'][] = &$datas[$val['id']];
            }else{
                $tree[] = &$datas[$val['id']];
            }
        }
        return $tree;
    }

    /* 获取格式化后的栏目tree操作 */
    public function formatTree () {
        $this->_formatTree();
        return $this->_formatTree;
    }

    /* 格式化操作操作 */
    private function _formatTree ($lists = array(), $level = 0 , $ext = '└') {
        if (!$lists) {
            $lists = S('DB_MENU_FORMAT');
            if (!$lists) {
                $lists = $this->lists();
                S('DB_MENU_FORMAT', $lists);
            }
        }
        $str = str_repeat('&nbsp', $level * 4);
        foreach ($lists as $key => $val) {
            $val['title'] = $str . $ext . $val['title'];
            if(array_key_exists('child', $val)){
                $child = $val['child'];
                unset($val['child']);
                array_push($this->_formatTree, $val);
                $this->_formatTree($child, $level+1);
            } else {
                array_push($this->_formatTree, $val);
            }
        }
    }


}
