<?php
namespace Index\Widget;
class IndexWidget extends \Think\Controller {
    public function index(){
        echo '测试';
    }

    public function message() {

        $this->display('Widget/Index/message');
    }
}
