<?php
//系统配置
namespace Admin\Controller;
use Admin\Common\AController;
class ConfigController extends AController {

    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Config/index').'">系统管理</a>';
    }

    /* 配置参数 */
    public function index() {
        $id     =   I('get.id',1);
        $type   =   C('CONFIG_GROUP_LIST');
        $list   =   M("Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->meta_title = '配置信息';
        $this->display();
    }

    /* 配置管理 */
    public function group(){
        /* 查询条件初始化 */
        $map = array();
        $map  = array('status' => 1);
        if(isset($_GET['group'])){
            $map['group']   =   I('group',0);
        }
        if(isset($_GET['name'])){
            $map['name']    =   array('like', '%'.(string)I('name').'%');
        }
        $list = $this->lists('Config', $map,'sort,id');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/group'));
        $this->assign('group',C('CONFIG_GROUP_LIST'));
        $this->assign('group_id',I('get.group',0));
        $this->assign('list', $list);
        $this->meta_title = '配置列表';
        $this->display();
    }

    /*  新增配置 */
    public function add(){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->add()){
                    S('DB_CONFIG_DATA',null);
                    action_log();
                    $this->success('新增成功', U('group'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info',null);
            $this->display('edit');
        }
    }

    /* 编辑配置 */
    public function edit($id = 0){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->save()){
                    S('DB_CONFIG_DATA',null);
                    action_log();
                    $this->success('更新成功', U('group'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);
            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /* 批量保存配置 */
    public function save($config){
        if($config && is_array($config)){
            $Config = M('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        action_log();
        $this->success('保存成功！');
    }

    /* 删除配置 */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Config')->where($map)->delete()){
            S('DB_CONFIG_DATA',null);
            action_log();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }



    /* 配置排序 */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');
            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(I('group')){
                $map['group']	=	I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '配置排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                action_log();
                $this->success('排序成功！',Cookie('__forward__'));
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
