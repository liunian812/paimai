<?php
namespace Admin\Controller;
use Admin\Common\AController;
class ShopController extends AController {
    private $db;
    private $type = 1;
    private $title = '店铺';

    public function _init () {
        //获取类名称
        $class_name = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('class_name', $class_name);
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($class_name . '/index').'">'. $this->title .'管理</a>';
        $this->db = D('User');
    }

    /* 列表 */
    public function index(){
        $map = $search = array(
            'status' => 1
        );
        $user_name = I('user_name');
        if (!empty($user_name)) {
            $map['user_name'] = array('like', '%'. $user_name .'%');
            $search['user_name'] = $user_name;
        }
        $shop_group = I('shop_group');
        if (!empty($shop_group)) {
            $map['shop_group'] = $shop_group;
            $search['shop_group'] = $shop_group;
        }
        $list = $this->lists('User', $map, 'update_time desc');
        $this->assign('list', $list);
        $this->assign('search', $search);
        $this->meta_title = $this->title . '列表';
        $this->display();
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
            $user_id = I('user_id',0,'intval');
            $info = $this->db->find($user_id);
            if (!$info) {
                $this->error('不存在！');
            } else {
                $this->assign('info', $info);
            }

            $this->meta_title = '更新' . $this->title;
            $this->display();
        }
    }

    /*  删除 */
    public function del() {
        $user_id = array_unique((array)I('user_id',0));
        //print_r($user_id); exit;
        if ( empty($user_id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('user_id' => array('in', $user_id) );
        if($this->db->where($map)->setField('status', 0)){
            action_log();
            $this->success('成功');
        } else {
            $this->error('失败！');
        }
    }

    /* 认证列表 */
    public function auth(){
        $map = $search = array(
            // 'status' => 1
        );
        $user_name = I('user_name');
        if (!empty($user_name)) {
            $map['user_name'] = array('like', '%'. $user_name .'%');
            $search['user_name'] = $user_name;
        }
        $shop_group = I('shop_group');
        if (!empty($shop_group)) {
            $map['shop_group'] = $shop_group;
            $search['shop_group'] = $shop_group;
        }
        $list = $this->lists('UserAuth', $map, 'status asc, update_time desc');
        foreach ($list as $key => $value) {
            // $list[$key]['user_info'] = get_shop_info($value['user_id']);
            $list[$key]['extend'] = unserialize($value['extend']);
        }
        $this->assign('list', $list);
        $this->assign('search', $search);
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/auth'));
        $this->meta_title = '认证列表';
        $this->display();
    }

    /* 认证详情 */
    public function authShow($id = 0){
        if (empty($id)) {
            $this->error('非法参数');
        }
        $info = M('UserAuth')->find($id);
        if (!$info) {
            $this->error('不存在！');
        } else {
            $info['extend'] = unserialize($info['extend']);
            $this->assign('info', $info);
        }
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/auth'));
        $this->meta_title = '认证详情';
        $this->display();
    }

    /* 认证审核 */
    public function authCheck($id = 0, $status = 0){
        if (empty($id)) {
            $this->error('非法参数');
        }
        $info = M('UserAuth')->find($id);
        if (!$info) {
            $this->error('不存在！');
        }
        if ($status == 2) {
            M('UserAuth')->save(array('id'=>$id, 'status'=>2, 'update_time'=>NOW_TIME));
            if ($info['type'] == 1) {
                M('User')->where(array('user_id'=>$info['user_id']))->setField('user_auth', 1);
            } else {
                M('User')->where(array('user_id'=>$info['user_id']))->setField('shop_auth', 1);
            }
            $data = array(
                'touser' => get_shop_info($info['user_id'], 'openid'),
                'msgtype' => 'news',
                'news' => array(
                    'articles' => array(
                        array(
                            'title' => '认证申请提示',
                            'description' => '状态：认证成功' . PHP_EOL . '执行时间：' .  date('Y-m-d H:i:s'),
                            'url' => C('WEB_SITE_URL'),
                            'picurl' => ''
                        )
                    )
                )
            );
            $this->WX->sendCustomMessage($data);
        } else {
            M('UserAuth')->save(array('id'=>$id, 'status'=>0, 'update_time'=>NOW_TIME));
            $data = array(
                'touser' => get_shop_info($info['user_id'], 'openid'),
                'msgtype' => 'news',
                'news' => array(
                    'articles' => array(
                        array(
                            'title' => '认证申请提示',
                            'description' => '状态：申请被拒绝' . PHP_EOL . '执行时间：' .  date('Y-m-d H:i:s'),
                            'url' => C('WEB_SITE_URL'),
                            'picurl' => ''
                        )
                    )
                )
            );
            $this->WX->sendCustomMessage($data);
        }
        $this->success('成功');
    }

    /* 更新缓存 */
    protected function updateCache() {

    }
}
