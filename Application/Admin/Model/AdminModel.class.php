<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model {
    protected $_validate = array(
        array('verify','check_verify','验证码错误！',1,'function',4),
        array('username','require','用户名必填！'),
        array('password','require','密码必填！',2),
        array('password','require','密码必填！',2,'',4)
    );

    protected $_auto = array(
        //array('password','','密码必填！')
    );

    /* 登录处理 */
    public function login () {
        if (!$this->create('', 4)) {
            return false;
        } else {
            $password = I('password');
            $user = $this->where(array('username'=>I('username')))->field(true)->find();
            if (!$user['status']) {
                $this->error = '该管理员已被禁用！';
                return false;
            }
            if ($user['password'] !== password_md5($password)) {
                $this->error = '密码错误！';
                return false;
            } else {
                /* 更新登录信息 */
                $data = array(
                    'user_id'             => $user['user_id'],
                    'login'           => array('exp', '`login`+1'),
                    'last_login_time' => NOW_TIME,
                    'last_login_ip'   => get_client_ip(1),
                );
                //print_r($data); exit;
                $this->save($data);
                /* 记录登录SESSION和COOKIES */
                $auth = array(
                    'uid'             => $user['user_id'],
                    'username'        => $user['username'],
                    'last_login_time' => $user['last_login_time'],
                );
                session('user_auth', $auth);
                session('user_auth_sign', data_auth_sign($auth));
                return true;
            }
        }
    }


    /* 插入操作 */
    public function input () {
        $username = I('post.username');
        $password = I('post.password');
        $group_id = I('post.group_id');
        $status = I('post.status');
        $data = array();
        if (empty($username)) {
            $this->error = '用户名必填';
            return false;
        }
        if (empty($group_id)) {
            $this->error = '用户组必填';
            return false;
        }
        if (empty($password)) {
            $this->error = '密码必填';
            return false;
        }
        $data['username'] = $username;
        $data['group_id'] = $group_id;
        $data['status'] = $status;
        $data['password'] = password_md5($password);
        $data['create_time'] = NOW_TIME;
        $data['update_time'] = NOW_TIME;
        $this->add($data);
        return true;
    }

    /* 更新操作 */
    public function update () {
        $user_id = I('post.user_id');
        $username = I('post.username');
        $password = I('post.password');
        $group_id = I('post.group_id');
        $status = I('post.status');
        $data = array();
        if (empty($user_id)) {
            $this->error = '参数错误';
            return false;
        }
        if (empty($username)) {
            $this->error = '用户名必填';
            return false;
        }
        if (empty($group_id)) {
            $this->error = '用户组必填';
            return false;
        }
        $data['username'] = $username;
        $data['user_id'] = $user_id;
        $data['group_id'] = $group_id;
        $data['status'] = $status;
        $data['update_time'] = NOW_TIME;
        if ($password) {
            $data['password'] = password_md5($password);
        }
        $this->save($data);
        return true;
    }

    /* 注销当前用户 */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }

}
