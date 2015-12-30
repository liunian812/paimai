<?php
namespace Admin\Controller;
use Admin\Common\AController;
use Org\Util\Database;
class DatabaseController extends AController {
    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Index/index').'">数据库</a>';
    }

    /* 空操作 */
    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('index');
    }

    public function index(){
        $list  =  M()->query('SHOW TABLE STATUS');
        $list  = array_map('array_change_key_case', $list);
        //print_r($list);
        $this->assign('list', $list);
        $this->meta_title = '数据库列表';
        $this->display();
    }

    public function lists(){
        $list = glob('./Data/bakup/*');
        foreach ($list as $key => $value) {
            $list[$key] = pathinfo($value);
            $list[$key]['url'] = $value;
        }
        //print_r($list);
        $this->assign('list', $list);
        $this->meta_title = '备份列表';
        $this->display();
    }

    public function export(){
        $tables = array_unique((array)I('id',0));
        if ( empty($tables) ) {
            $this->error('请选择要操作的数据!');
        }
        $config = array(
                'path'     => realpath('./Data/bakup') . DIRECTORY_SEPARATOR,
                'part'     => 1024000000, //1G
                'compress' => 1,
                'level'    => 5,
            );
        //生成备份文件信息
        $file = array(
            'name' => date('Ymd-His', NOW_TIME),
            'part' => 1,
        );
        //创建备份文件
        $Database = new Database($file, $config);
        if(false === $Database->create()){
            $this->error('初始化失败，备份文件创建失败！');
        }
        $start = 0;
        foreach ($tables as $value) {
            $start = $Database->backup($value, $start);
        }
        if(false === $start){
            $this->error('备份出错！');
        } elseif (0 === $start) {
            $this->success('备份完成！');
        }
    }

    public function del(){
        $files = array_unique((array)I('id',0));
        if ( empty($files) ) {
            $this->error('请选择要操作的数据!');
        }
        foreach ($files as $value) {
            unlink($value);
        }
        $this->success('删除完成！');
    }
}
