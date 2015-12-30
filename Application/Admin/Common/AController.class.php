<?php
namespace Admin\Common;
use Think\Controller;
use Vendor\TPWechat;
class AController extends Controller{
    static $Auth = null;

    protected $_menu = array();

    /* 初始化 */
    public function _initialize () {
        if(!defined('UID')) {
            define('UID', is_login());
        }
        // 还没登录 跳转到登录页面
        if(!UID ){
            $this->redirect('Public/login');
        }
        //批量添加配置
        $config = S('DB_CONFIG_DATA');
        if(!$config){
            $config = D('config')->lists();
            S('DB_CONFIG_DATA', $config);
        }
        C($config);

        $this->options = array(
            'token' => C('TOKEN'), //填写你设定的key
            'encodingaeskey' => '', //填写加密用的EncodingAESKey
            'appid' => C('APPID'), //填写高级调用功能的app id
            'appsecret' => C('APPSECRET') //填写高级调用功能的密钥
        );
        //echo '<pre>'; print_r($this->options); exit;
        $this->WX = new TPWechat($this->options);

        //超级管理员
        if (in_array(UID, C('IS_ROOT'))) {
            define('IS_ROOT', 1);
        } else {
            define('IS_ROOT', 0);
        }
        //超级管理员不受ip限制
        if(!IS_ROOT && C('ADMIN_ALLOW_IP')){
            if(!in_array(get_client_ip(),explode(',',C('ADMIN_ALLOW_IP')))){
                //session('user_auth', null);
                //session('user_auth_sign', null);
                $this->error('IP:禁止访问', U('Index/index'));
            }
        }
        //检测访问权限
        self::$Auth = new \Think\Auth();
        $rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        $this->assign('__ACT__', $rule);
        // 超级权限
        if (!IS_ROOT && !$this->_checkRule($rule)) {
            session('user_auth', null);
            session('user_auth_sign', null);
            $this->error('没有权限！', U('Public/login'));
        }
        if (IS_ROOT) {
            $this->assign('__MENU__', D('Menu')->lists(array('status'=>1)));
        } else {
            $this->assign('__MENU__', $this->_getMenu());
        }
        // 执行类初始化方法最好不要用__construct
        $this->_init();
    }

    public function _init() {}

    final private function _getMenu () {
        $rules = self::$Auth->authListh(UID);
        //print_r($menus);
        $datas = $tree =  array();
        foreach ($rules as $key => $val) {
            $datas[$val['id']] = $val;
        }
        foreach ($datas as $key => $val) {
            if(isset($datas[$val['pid']])){
                $datas[$val['pid']]['child'][] = &$datas[$val['id']];
            }else{
                $tree[] = &$datas[$val['id']];
            }
        }
        return $tree;
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @return boolean
     */
    final private function _checkRule($rule){
        if(!self::$Auth->check($rule, UID, 1, 'url')){
            return false;
        }
        return true;
    }



    /**
     * 通用分页列表数据集获取方法
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $param    =   (array)I('get.');
        if(is_string($model)){
            $model  =   M($model);
        }
        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);
        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($param['_order']) && isset($param['_field']) && in_array(strtolower($param['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$param['_field'].'` '.$param['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($param['_order'],$param['_field']);
        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();
        if( isset($param['r']) ){
            $listRows = (int)$param['r'];
        }else{
            $listRows = C('ADMIN_LIST_ROWS') > 0 ? C('ADMIN_LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $param);
        if($total>$listRows){
            $page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
            //$page->setConfig('prev', '上一页');
            //$page->setConfig('next', '下一页');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    }
}
