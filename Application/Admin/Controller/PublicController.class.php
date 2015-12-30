<?php
namespace Admin\Controller;
class PublicController extends \Think\Controller {

    /* 登录 */
    public function login() {
        if (is_login()) {
            $this->redirect('Index/index');
        } else {
            if (IS_POST) {
                $Admin = D('Admin');
                if ($Admin->login()) {
                    $this->success('登录成功', U('Index/index'));
                } else {
                    $this->error($Admin->getError());
                }
            } else {
                layout(false); // 临时关闭当前模板的布局功能
                $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            D('Admin')->logout();
            session('[destroy]');
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }

    /* 验证码 */
    public function verify(){
        $verify = new \Think\Verify();
        $verify->length = 4;
        $verify->fontSize = 25;
        $verify->entry();
    }
}
