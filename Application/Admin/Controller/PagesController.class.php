<?php
namespace Admin\Controller;
use Admin\Common\AController;
class PagesController extends AController {
    private $db;
    private $type = 'pages';

    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Pages/index').'">单页管理</a>';
        $this->db = D('pages');
        $this->assign('type', $this->type);
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('index');
    }

    /* 列表 */
    public function index(){

        $map = array();
        $map['status'] = 1;
        $map['type'] = $this->type;
        //$list = $this->lists('category', $map, 'sort desc, id desc');
        //$this->assign('list', $list);
        $this->assign('list', D('category')->formatTree());
        $this->assign('category', D('category')->getAll());
        $this->meta_title = '单页列表';
        $this->display();
    }

    /* 修改 */
    public function edit() {
        if (IS_POST) {
            if(I('edit')) {
                if (!$this->db->update()) {
                    $this->error($this->db->getError());
                } else {
                    action_log();
                    $this->updateCache();
                    $this->success('更新成功', U('index'));
                }
            } else {
                if (!$this->db->input()) {
                    $this->error($this->db->getError());
                } else {
                    action_log();
                    $this->updateCache();
                    $this->success('新增成功', U('index'));
                }
            }
        } else {
            $catid = I('catid',0,'intval');
            if (empty($catid)) {
                $this->error('参数错误...');
            }
            $info = $this->db->find($catid);
            if (!$info) {
                $this->assign('edit', 0);
                $this->assign('info', array('content'=>''));
            } else {
                $this->assign('edit', 1);
                $this->assign('info', $info);
            }
            $this->assign('cate_info', D('category')->find($catid));
            $this->meta_title = '更新单页';
            $this->display();
        }
    }

    /* 更新缓存 */
    protected function updateCache() {

    }
}
