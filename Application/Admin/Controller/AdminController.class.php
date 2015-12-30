<?php
namespace Admin\Controller;
use Admin\Common\AController;
class AdminController extends AController {
    public $db = null;

    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Admin/index').'">管理员</a>';
        $this->db = D('Admin');
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('Index/index');
    }

    /* 管理员列表 */
    public function index(){
        $list = $this->db->select();
        $group_list = D('AuthGroup')->lists();
        $this->assign('list', $list);
        $this->assign('group_list', $group_list);
        $this->meta_title = '管理员';
        $this->display();
    }

    /* 新增 */
    public function add() {
        if (IS_POST) {
            if (!$this->db->input()) {
                $this->error($this->db->getError());
            } else {
                action_log();
                $this->success('新增成功');
            }
        } else {
            $group_list = D('AuthGroup')->lists();
            $this->assign('group_list', $group_list);
            $this->meta_title = '新增管理员';
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
                $this->success('更新成功');
            }
        } else {
            $user_id = I('user_id',0,'intval');
            $info = $this->db->find($user_id);
            if (!$info) {
                $this->error('不存在！');
            } else {
                $this->assign('info', $info);
            }
            $group_list = D('AuthGroup')->lists();
            $this->assign('group_list', $group_list);
            $this->meta_title = '更新管理员';
            $this->display("edit");
        }
    }

    /*  删除 */
    public function del() {
        $user_id = array_unique((array)I('user_id',0));
        if ( empty($user_id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('user_id' => array('in', $user_id) );
        if($this->db->where($map)->delete()){
            action_log();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}
