<?php
namespace Admin\Controller;
use Admin\Common\AController;
class AuthController extends AController {
    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Auth/index').'">权限管理</a>';
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('Index/index');
    }

    /* 列表 */
    public function index(){
        $list = D('AuthGroup')->lists();
        $this->assign('list', $list);
        $this->meta_title = '管理组';
        $this->display();

    }

    /* 新增 */
    public function add() {
        if (IS_POST) {
            $db = D('AuthGroup');
            if (!$db->input()) {
                $this->error($db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('新增成功');
            }
        } else {
            $this->meta_title = '新增管理组';
            $this->display("edit");
        }
    }

    /* 修改 */
    public function edit() {
        if (IS_POST) {
            $db = D('AuthGroup');
            if (!$db->update()) {
                $this->error($db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('更新成功');
            }
        } else {
            $id = I('id',0,'intval');
            $info = D('AuthGroup')->find($id);
            if (!$info) {
                $this->error('不存在！');
            } else {
                $this->assign('info', $info);
            }
            $this->meta_title = '更新管理组';
            $this->display();
        }
    }

    /* 权限分配 */
    public function access($id = 0) {
        if (empty($id)) {
            $this->error('管理组不能为空！');
        }
        if (IS_POST) {
            $rules = array_unique((array)I('rules'));
            $data = array(
                'id' => $id,
                'rules' => implode(',', $rules)
            );
            $result = D('AuthGroup')->save($data);
            if ($result) {
                action_log();
                $this->updateCache();
                $this->success('成功');
            } else {
                $this->error('失败');
            }

        } else {
            $list =  D('Menu')->lists(array('status'=>1));
            $info = D('AuthGroup')->find($id);
            $this->assign('list', $list);
            $this->assign('info', $info);
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
        if(D('AuthGroup')->where($map)->delete()){
            action_log();
            $this->updateCache();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function updateCache() {

        S('DB_AUTHGROUP_DATA', null);
    }
}
