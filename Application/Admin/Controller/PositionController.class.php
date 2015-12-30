<?php
namespace Admin\Controller;
use Admin\Common\AController;
class PositionController extends AController {
    private $db;
    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Position/index').'">推荐管理</a>';
        $this->db = D('position');
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('index');
    }

    /* 列表 */
    public function index(){

        $map = array();
        $list = $this->lists('Position', $map, 'sort desc, id desc');
        $this->assign('cateType', C('CATEGORY_TYPE'));
        $this->assign('list', $list);
        $this->meta_title = '推荐位列表';
        $this->display();
    }

    public function dList($id) {

        $info = $this->db->find($id);
        $map = array();
        $map['id'] = array('in', $info['data']);
        $list = $this->lists($info['type'], $map, 'sort desc, id desc');
        $this->assign('info', $info);
        $this->assign('list', $list);
        $this->meta_title = '信息列表';
        $this->display();
    }

    public function dAdd($pid = 0, $id = 0) {
        if (empty($pid)) {
            $this->error('参数错误');
        }
        $id = (array)$id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $info = $this->db->find($pid);
        $data = explode(',', $info['data']);
        $data =array_unique(array_merge($data, $id));
        $data = implode(',', $data);
        $map = array('id' => $pid);
        //print_r($map);
        //print_r($id);
        //exit;
        if($this->db->where($map)->setField('data', $data)){

            action_log();
            $this->success('推荐成功');
        } else {
            echo $this->db->_sql();
            $this->error('推荐失败！');
        }
    }

    /*  删除 */
    public function dDel($pid = 0, $id = 0) {
        if (empty($pid)) {
            $this->error('参数错误');
        }
        $info = $this->db->find($pid);
        $data = explode(',', $info['data']);
        foreach ($data as $key => $val) {
            if ($val == $id) {
                unset($data[$key]);
            }
        }
        $data = implode(',', $data);
        $map = array('id' => $pid);
        if($this->db->where($map)->setField('data', $data)){
            action_log();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
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
            $this->assign('cateType', C('CATEGORY_TYPE'));
            $this->meta_title = '新增推荐位';
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
                $this->error('数据不存在或已经被删除');
            }
            $this->assign('info', $info);
            $this->meta_title = '更新推荐位';
            $this->display();
        }
    }

    /*  删除 */
    public function del() {
        $id = array_unique((array)I('id',0));
        //print_r($id); exit;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id) );
        if($this->db->where($map)->delete()){
            action_log();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /* 更新缓存 */
    protected function updateCache() {

    }
}
