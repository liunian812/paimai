<?php
namespace Admin\Controller;
use Admin\Common\AController;
class NewsController extends AController {
    private $db;
    private $type;
    private $title = '新闻';
    private $configs = array(
        'allow_thumb'       => 1,   //图片
        'allow_images'      => 0,   //图片集合
        'allow_desc'        => 1,   //描述
        'allow_content'     => 1,   //内容
        'allow_extends'     => 0,   //扩展
        'allow_create_time' => 1,   //发布时间
    );
    private $extends = array(
        'field_1' => array(
            'type' => 'text',
            'title' => '单文本',
            'desc' => '扩展描述',
        ),
        'field_2' => array(
            'type' => 'textarea',
            'title' => '多文本',
            'desc' => '扩展描述',
        ),
        'field_3' => array(
            'type' => 'thumb',
            'title' => '单图片',
            'desc' => '建议图片尺寸：230*200px',
        ),
        'field_4' => array(
            'type' => 'content',
            'title' => '富文本',
            'width' => '800',
            'height' => '200',
        )
    );

    public function _init () {
        //获取类名称
        $this->type = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($this->type . '/index').'">'. $this->title .'管理</a>';
        $this->db = D('News');
        $this->assign('type', $this->type);
        $this->assign('configs', $this->configs);
        $this->assign('extends', $this->extends);
    }

    /* 列表 */
    public function index(){
        $map = $search = array(
            'status' => 1,
            'type' => $this->type,
        );
        $catid = I('catid', 0, 'intval');
        if (!empty($catid)) {
            $map['catid'] = $catid;
            $search['catid'] = $catid;
        }
        $title = I('title');
        if (!empty($title)) {
            $map['title'] = array('like', '%'. $title .'%');
            $search['title'] = $title;
        }
        $list = $this->lists('News', $map, 'sort desc, id desc');
        $this->assign('list', $list);
        $fields = M()->query('SHOW FULL COLUMNS FROM __NEWS__');
        $positions = array();
        foreach ($fields as $key => $value) {
            if (strpos($value['field'], 'position_') !== false) {
                $positions[$value['field']] = $value['comment'];
            }
        }
        unset($fields);
        $this->assign('positions', $positions);
        $this->assign('category', D('category')->getAll());
        $this->assign('cate_list', D('category')->formatTree());
        $this->assign('search', $search);
        $this->meta_title = $this->title . '列表';
        $this->display('Sping/index');
    }

    /* 新增 */
    public function add($catid = 0) {
        if (IS_POST) {
            if (!$this->db->input()) {
                $this->error($this->db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('新增成功', U('index'));
            }
        } else {
            $cate_list = D('category')->formatTree();
            foreach ($cate_list as $key => $value) {
                if ($value['id'] == $catid) {
                    $this->assign('cate_info', $value);
                    break;
                } else {
                    continue;
                }
            }
            $this->assign('cate_list', $cate_list);

            $this->assign('info', array('type'=>$this->type,'catid'=>I('catid')));
            $this->meta_title = '新增' . $this->title;
            $this->display('Sping/add');
        }
    }

    /* 修改 */
    public function edit($catid = 0) {
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
                $info['extends'] = unserialize($info['extends']);
                $this->assign('info', $info);
            }
            $cate_list = D('category')->formatTree();
            foreach ($cate_list as $key => $value) {
                if ($value['id'] == $info['catid']) {
                    $this->assign('cate_info', $value);
                    break;
                } else {
                    continue;
                }
            }
            $this->assign('cate_list', $cate_list);
            $this->meta_title = '更新' . $this->title;
            $this->display('Sping/edit');
        }
    }
    /* 批量推荐 */
    public function position($id = 0, $position = '', $status = 0) {
        if (empty($id) || empty($position)) {
            $this->error('参数错误');
        }
        if($this->db->where(array('id'=>$id))->setField($position, $status)){
            action_log();
            $this->success('成功');
        } else {
            $this->error('失败！');
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
