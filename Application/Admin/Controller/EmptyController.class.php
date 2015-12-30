<?php
namespace Admin\Controller;
use Admin\Common\AController;
class EmptyController extends AController {
    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Empty/index').'">空控制器</a>';
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('Index/index');
    }

    public function index(){
        $this->meta_title = '空操作';
        $this->display('Index/index');
    }


}
