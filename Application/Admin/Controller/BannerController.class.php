<?php
namespace Admin\Controller;
use Admin\Common\AController;
class BannerController extends AController {
    private $db;
    private $type;
    private $title = '广告位';
    private $dTitle = '广告信息';

    public function _init () {
        //获取类名称
        $this->type = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($this->type . '/index').'">'. $this->title .'管理</a>';
        $this->db = D($this->type);
        $this->dDb = D($this->type . 'Data');
        $this->assign('type', $this->type);
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('index');
    }

    /* 列表 */
    public function dList(){
        $map = array();
        $bid = I('bid', 0, 'intval');
        if (!empty($bid)) {
            $map['bid'] = $bid;
        }
        $banner_info = D('Banner')->find($bid);
        $list = $this->lists($this->type . 'Data', $map, 'sort desc, id desc');
        $this->assign('list', $list);
        $this->assign('bid', $bid);
        $this->assign('banner_info', $banner_info);
        $this->meta_title = $this->dTitle . '列表';
        $this->display();
    }

    /* 新增 */
    public function dAdd() {
        if (IS_POST) {
            if (!$this->dDb->input()) {
                $this->error($this->dDb->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('新增成功', U('dList?bid=' . I('bid')));
            }
        } else {
            $bid = I('bid', 0, 'intval');
            $this->assign('info', array('bid'=>$bid));
            $banner_info = $this->db->find($bid);
            $this->assign('banner_info', $banner_info);
            $this->meta_title = '新增' . $this->dTitle;
            $this->display("dEdit");
        }
    }

    /* 修改 */
    public function dEdit() {
        if (IS_POST) {
            if (!$this->dDb->update()) {
                $this->error($this->dDb->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('更新成功', U('dList?bid=' . I('bid')));
            }
        } else {
            $id = I('id',0,'intval');
            $info = $this->dDb->find($id);
            if (!$info) {
                $this->error('不存在！');
            }
            $banner_info = $this->db->find($info['bid']);
            $this->assign('banner_info', $banner_info);
            $this->assign('info', $info);
            $this->meta_title = '更新' . $this->dTitle;
            $this->display();
        }
    }

    /*  删除 */
    public function dDel() {
        $id = array_unique((array)I('id',0));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id) );
        if($this->dDb->where($map)->delete()){
            action_log();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /* 列表 */
    public function index(){

        $map = array();
        $map['status'] = 1;
        $id = I('id', 0, 'intval');
        if (!empty($id)) {
            $map['id'] = I('id');
        }
        $list = $this->lists($this->type, $map, 'sort desc, id desc');
        $this->assign('list', $list);
        $this->meta_title = $this->title . '列表';
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
            $this->assign('cate_list', D('category')->formatTree());
            $this->assign('info', array('id'=>I('id')));
            $this->meta_title = '新增' . $this->title;
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
                $info['images'] = unserialize($info['images']);
                $this->assign('info', $info);
            }
            $this->assign('cate_list', D('category')->formatTree());
            $this->meta_title = '更新' . $this->title;
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
