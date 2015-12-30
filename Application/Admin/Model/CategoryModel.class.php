<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model {

    private $_formatTree = array();

    //修改表名称
    protected $tableName = 'category';

    /* 自动验证 */
    protected $_validate = array(
        array('title','require','栏目名称必填！'),
        array('type','require','栏目类型必填！')
    );

    /* 自动完成 */
    protected $_auto = array(
        array('setting', 'callbackSetting', 3, 'callback')
    );

    public function callbackSetting($data) {
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

    /* 获取所有栏目 */
    public function getAll($map = array(), $order = '', $field = true) {
        $cates = $this->where($map)->field($field)->order($order)->select();
        $datas = $tree =  array();
        foreach ($cates as $key => $val) {
            $val['setting'] = unserialize($val['setting']);
            $datas[$val['id']] = $val;
        }
        return $datas;
    }

    /* 获取栏目tree操作 */
    public function lists ($map = array()) {
        $datas = $this->getAll($map, 'sort asc, id asc');
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
            $lists = S('DB_CATE_FORMAT');
            if (!$lists) {
                $lists = $this->lists();
                S('DB_CATE_FORMAT', $lists);
            }
        }
        $str = str_repeat('&nbsp', $level * 4);
        foreach ($lists as $key => $val) {
            $val['title'] = $str . $ext . $val['title'];
            if(array_key_exists('child', $val)){
                $child = $val['child'];
                $val['cat'] = 1;
                unset($val['child']);
                array_push($this->_formatTree, $val);
                $this->_formatTree($child, $level+1);
            } else {
                $val['cat'] = 0;
                array_push($this->_formatTree, $val);
            }
        }
    }


}
