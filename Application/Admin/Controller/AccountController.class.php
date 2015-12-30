<?php
namespace Admin\Controller;
use Admin\Common\AController;
class AccountController extends AController {
    private $db;
    private $type = 1;
    private $title = '账户';

    public function _init () {
        //获取类名称
        $class_name = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('class_name', $class_name);
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($class_name . '/index').'">'. $this->title .'管理</a>';
        $this->db = D('Tixian');
    }

    /* 提现列表 */
    public function index(){
        $map = $search = array();
        $status = I('status');
        if (!empty($status)) {
            $map['status'] = $status;
            $search['status'] = $status;
        }
        $list = $this->lists('Tixian', $map, 'status asc, id desc');
        $this->assign('list', $list);
        $this->assign('search', $search);
        $this->meta_title = '提现列表';
        $this->display();
    }

    // 提现审核
    public function change($id, $status){
        if (empty($id) || empty($status)) {
            $this->error('参数错误...');
        }
        $data = array(
            'id' => $id,
            'status' => $status,
            'update_time' => NOW_TIME
        );
        if($this->db->save($data)){
            switch ($status) {
                case '2':
                    $info = $this->db->find($id);
                    fund_inc($info['user_id'], $info['money'], '提现拒绝');
                    break;
                case '3':
                    // TODO: 企业转账接口
                    break;
            }

            $this->success('成功');
        } else {
            $this->error('失败！');
        }
    }

    /* 红包列表 */
    public function hongbao(){
        $map = $search = array();
        $status = I('status');
        if (!empty($status)) {
            $map['status'] = $status;
            $search['status'] = $status;
        }
        $list = $this->lists('Hongbao', $map, 'id desc');
        $this->assign('list', $list);
        $this->assign('search', $search);
        $this->meta_title = '红包列表';
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/hongbao'));
        $this->display();
    }

    /* 更新缓存 */
    protected function updateCache() {

    }
}
