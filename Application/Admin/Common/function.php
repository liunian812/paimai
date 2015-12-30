<?php
/**
 *    后台共用函数库  2015年6月29日 16:15:03
 */


/**
 * 行为记录
 * @param string
 * @return string
 */
function action_log(){
    $log  = UID . ':' . strtolower(CONTROLLER_NAME.'/'.ACTION_NAME) . PHP_EOL;
    file_put_contents('./log.txt', $log, FILE_APPEND);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 拥抱 <572300808@qq.com>
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 拥抱 <572300808@qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 获取登录用户名
 * @return string 用户名
 * @author 拥抱 <572300808@qq.com>
 */
function get_username() {
    $user = session('user_auth');
    if (is_array($user)) {
        return $user['username'];
    } else {
        return '';
    }
}
