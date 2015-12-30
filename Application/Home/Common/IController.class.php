<?php
namespace Home\Common;
use Vendor\TPWechat;
class IController extends \Think\Controller {
    private $options = array();
    public $WX = null;
    public $user_info = array();

    public function _initialize () {
        //批量添加配置
        $config = S('DB_CONFIG_DATA');
        if(!$config){
            $config = D('Admin/Config')->lists();
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

        // 执行类初始化方法最好不要用__construct
        $this->_init();
    }

    protected function _init() {
        //cookie('user_id', null);
        $user_id = cookie('user_id');
        if ($user_id) {
            cookie('user_id', $user_id);
            $user_info = M('User')->find($user_id);
            if (!$user_info) {
                cookie('user_id', null);
                $this->redirect(__SELF__);
            }
            $user_info['area'] = json_decode($user_info['area'], true);
        } else {
            if (empty($_GET['code'])) {
                $callback = C('WEB_SITE_URL') . __SELF__;
                $url = $this->WX->getOauthRedirect($callback, '123456', 'snsapi_userinfo');
                redirect($url);
            }
            $access_data = $this->WX->getOauthAccessToken();
            $wx_info = $this->WX->getOauthUserinfo($access_data['access_token'], $access_data['openid']);
			if (empty($wx_info['openid'])) {
				$this->redirect(__SELF__);
			}
            $user_info = M('User')->where(array('openid' => $wx_info['openid']))->find();
            if (!$user_info) {
                $wx_info['nickname'] = remove_emoji($wx_info['nickname']);
                $user_info = array(
                    'openid' => $wx_info['openid'],
                    'user_name' => $wx_info['nickname'],
                    'user_avatar' => $wx_info['headimgurl'],
                    'sex' => $wx_info['sex'],
                    'create_time' => NOW_TIME,
                    'update_time' => NOW_TIME,
                    'status' => 1
                );
                $user_info['user_id'] = M('User')->add($user_info);
            }
            cookie('user_id', $user_info['user_id']);
        }
        $this->user_info = $user_info;
        $this->assign('user_info', $user_info);
        // 微信JSJDK
        // echo '<pre>';
        // print_r($this->WX->getJsSign('http://wwb.sypole.com' . __SELF__)); exit('</pre>');
        $this->assign('wx_config', $this->WX->getJsSign(C('WEB_SITE_URL') . __SELF__));
    }

    // 返回成功json
    protected function resultSuccess($info = '成功', $value = 0, $url = ''){
        $result = array(
            'resultMessage' => $info,
            'value' => $value,
            'resultCode' => 1,
            'goUrl' => $url
        );
        $this->ajaxReturn($result);
    }
    // 返回失败json
    protected function resultError($info = '失败', $value = 0, $url = ''){
        $result = array(
            'resultMessage' => $info,
            'value' => $value,
            'resultCode' => -1,
            'goUrl' => $url
        );
        $this->ajaxReturn($result);
    }

    protected function sendOrder($openid, $url, $data){
        $temp = array(
            'touser' => $openid,
            'template_id' => 'hIPbD2pnKDr-oUt5LdR3tFlAn2WaFdLi8KTRzSrvgH0',
            'url' => C('WEB_SITE_URL').$url,
            'topcolor' => '#FF0000',
            'data' => array (
                'first' => array (
                  'value' => $data['first'],
                  'color' => '',
                ),
                'keyword1' => array (
                  'value' => $data['order_id'],
                  'color' => '',
                ),
                'keyword2' => array (
                  'value' => $data['order_status'],
                  'color' => '#FF0000',
                ),
                'keyword3' => array (
                  'value' => $data['product_name'],
                  'color' => '',
                ),
                'remark' => array (
                  'value' => $data['end_time'],
                  'color' => '',
                ),
            ),
        );
		addWeixinLog(var_export($temp, true));
        $this->WX->sendTemplateMessage($temp);
    }

    protected function sendBid($openid, $url, $data){
        $temp = array(
            'touser' => $openid,
            'template_id' => 'xblEk1F4edm-hGGNFZYf21LQVA7psayV2AEJGS1N3hc',
            'url' => C('WEB_SITE_URL').$url,
            'topcolor' => '#FF0000',
            'data' => array (
                'first' => array (
                  'value' => $data['first'],
                  'color' => '',
                ),
                'number' => array (
                  'value' => $data['product_id'],
                  'color' => '',
                ),
                'name' => array (
                  'value' => $data['product_name'],
                  'color' => '',
                ),
                'remark' => array (
                  'value' => $data['remark'],
                  'color' => '',
                ),
            ),
        );
        $this->WX->sendTemplateMessage($temp);
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
    protected function getAll($model, $where = array(), $order = '', $page_num = 0){
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
            if (empty($page_num)) {
                $listRows = C('SHOW_LIST_ROWS') > 0 ? C('SHOW_LIST_ROWS') : 10;
            } else {
                $listRows = $page_num;
            }

        }
        $page = new \Think\Page($total, $listRows, $param);
        if($total>$listRows){
            $page->setConfig('theme',' %UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% ');
            $page->rollPage = 7;
            $page->setConfig('prev', '←上一页');
            $page->setConfig('next', '下一页→');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    }

}
?>
