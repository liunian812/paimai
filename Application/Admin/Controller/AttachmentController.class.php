<?php
namespace Admin\Controller;
use Admin\Common\AController;
class AttachmentController extends AController {
    protected $uploads_path = 'Uploads';

    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Attachment/index').'">附件管理</a>';
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('Index/index');
    }

    public function index($dir = ''){
        $dir = base64_decode($dir);
        if (empty($dir) || $dir == $this->uploads_path) {
            $dir_path = $this->uploads_path;
            $flags = 0;
        } else {
            $dir = str_replace(array('..\\', '../', './', '.\\', '.'), '', trim($dir));
            $dir_path = $dir;
            $flags = 1;
            if (strpos($this->uploads_path, $dir) != 0) {
                $this->error('没有操作该目录的权限');
            }
        }
        $lists = glob($dir_path . '/*');
        $this->assign('flags', $flags);
        $this->assign('dir_path', $dir_path);
        $this->assign('list', $lists);
        $this->meta_title = '附件管理';
        $this->display();
    }

    public function delFile($dir = '') {
        $dir = base64_decode($dir);
        if (empty($dir) || $dir == $this->uploads_path) {
            $this->error('参数错误');
        } else {
            $dir = str_replace(array('..\\', '../', './', '.\\'), '', trim($dir));
            $dir_path = $dir;
            if (strpos($this->uploads_path, $dir) != 0) {
                $this->error('没有操作该目录的权限');
            }
            if(unlink($dir)){
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

}
