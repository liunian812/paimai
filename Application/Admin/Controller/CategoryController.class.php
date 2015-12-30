<?php
namespace Admin\Controller;
use Admin\Common\AController;
class CategoryController extends AController {
    private $db;

    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Category/index').'">栏目管理</a>';
        $this->db = D('category');
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('index');
    }

    /* 列表 */
    public function index(){
        $this->assign('cateType', C('CATEGORY_TYPE'));
        $this->assign('list', $this->db->formatTree());
        $this->meta_title = '栏目列表';
        $this->display();
    }

    /* 新增 */
    public function add() {
        if (IS_POST) {
            if (!$this->db->input()) {
                $this->error($this->db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('新增成功', U('index'));
            }
        } else {
            $this->assign('info', array('pid'=>I('pid')));
            $this->assign('list', $this->db->formatTree());
            $this->assign('cateType', C('CATEGORY_TYPE'));
            $this->meta_title = '新增栏目';
            $this->display("edit");
        }
    }

    /* 修改 */
    public function edit() {
        if (IS_POST) {
            if (!$this->db->update()) {
                $this->error($this->db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('更新成功', U('index'));
            }
        } else {
            $id = I('id',0,'intval');
            $info = $this->db->find($id);
            if (!$info) {
                $this->error('不存在！');
            } else {
                $info['setting'] = unserialize($info['setting']);
                $this->assign('info', $info);
            }
            if ($info['type'] != 'Pages') {
                $count = D($info['type'])->where(array('catid'=>$info['id']))->count();
            }
            $this->assign('count', $count);
            $this->assign('list', $this->db->formatTree());
            $this->assign('cateType', C('CATEGORY_TYPE'));
            $this->meta_title = '更新栏目';
            $this->display();
        }
    }

    /*  删除 */
    public function del() {
        $id = array_unique((array)I('id',0));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id) );
        if($this->db->where($map)->delete()){
            action_log();
            $this->updateCache();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /* 更新缓存 */
    protected function updateCache() {
        S('DB_CATE_FORMAT', null);
        S('CATEGORYS', null);
        S('NAVS', null);
    }
}
