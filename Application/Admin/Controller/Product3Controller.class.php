<?php
namespace Admin\Controller;
use Admin\Common\AController;
class Product3Controller extends AController {
    private $db;
    private $type = 3;
    private $title;

    public function _init () {
        $product_type = C('PRODUCT_TYPE');
        $this->title = $product_type[$this->type];
        //获取类名称
        $class_name = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($class_name . '/index').'">'. $this->title .'管理</a>';
        $this->db = D('Product');
        $this->assign('class_name', $class_name);
    }

    /* 列表 */
    public function index(){
        $map = $search = array(
            'status' => 1,
            'product_type' => $this->type,
            'begin_time' => array('lt', NOW_TIME),
            'end_time' => array('gt', NOW_TIME),
        );
        $title = I('title');
        if (!empty($title)) {
            $map['product_name'] = array('like', '%'. $title .'%');
            $search['title'] = $title;
        }
        $cate = I('cate');
        if (!empty($cate)) {
            $map['product_cate'] = $cate;
            $search['product_cate'] = $cate;
        }
        $list = $this->lists('Product', $map, 'update_time desc');
        foreach ($list as $key => $value) {
            $list[$key]['product_img'] = array_shift(json_decode($value['product_images'], true));
        }
        $this->assign('list', $list);
        $fields = M()->query('SHOW FULL COLUMNS FROM __PRODUCT__');
        $positions = array();
        foreach ($fields as $key => $value) {
            if (strpos($value['field'], 'position_') !== false) {
                $positions[$value['field']] = $value['comment'];
            }
        }
        unset($fields);
        $this->assign('positions', $positions);
        $this->assign('search', $search);
        $this->meta_title = $this->title . '列表';
        $this->display('Product/index');
    }

    /* 批量推荐 */
    public function position($product_id = 0, $position = '', $status = 0) {
        if (empty($product_id) || empty($position)) {
            $this->error('参数错误');
        }
        if($this->db->where(array('product_id'=>$product_id))->setField($position, $status)){
            action_log();
            $this->success('成功');
        } else {
            $this->error('失败！');
        }
    }

    /* 排序 */
    public function sort($product_id = 0, $sort = 0) {
        if (empty($product_id)) {
            $this->error('参数错误');
        }
        if($this->db->where(array('product_id'=>$product_id))->setField('sort', $sort)){
            action_log();
            $this->success('成功');
        } else {
            $this->error('失败！');
        }
    }

    /*  删除 */
    public function del() {
        $product_id = array_unique((array)I('product_id',0));
        //print_r($product_id); exit;
        if ( empty($product_id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('product_id' => array('in', $product_id) );
        if($this->db->where($map)->setField('status', 0)){
            action_log();
            $this->success('下架成功');
        } else {
            $this->error('下架失败！');
        }
    }

    /* 更新缓存 */
    protected function updateCache() {

    }
}
