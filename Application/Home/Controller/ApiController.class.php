<?php
namespace Home\Controller;
use Home\Common\IController;
use Vendor\TPWechat;
class ApiController extends IController {
    protected function _init() {}

    public function sendCustom($id = 1){
        $openid = get_shop_info($id, 'openid');
        $data = array(
            'touser' => $openid,
            'msgtype' => 'news',
            'news' => array(
                'articles' => array(
                    array(
                        'title' => 'Happy Day',
                        'description' => 'Is Really A Happy Day',
                        'url' => 'URL',
                        'picurl' => 'PIC_URL'
                    ),
                    array(
                        'title' => 'Happy Day',
                        'description' => 'Is Really A Happy Day',
                        'url' => 'URL',
                        'picurl' => 'PIC_URL'
                    ),
                    array(
                        'title' => 'Happy Day',
                        'description' => 'Is Really A Happy Day',
                        'url' => 'URL',
                        'picurl' => 'PIC_URL'
                    )
                )
            )
        );
        $this->WX->sendCustomMessage($data);
        $this->show(成功);
    }

    public function cron(){
        $Product = M('Product');
        $ProductBid = M('ProductBid');

        /* 5分钟提示 */
        $map = array('product_type' => array('in', '1,2'), 'is_send' => 0, 'status' => 1, 'end_time' => array('elt', NOW_TIME + 300));
        $product_lists = $Product->where($map)->select();
        print_r($product_lists);
        foreach ($product_lists as $value) {
			// $Product->where(array('product_id'=>$value['product_id']))->setField('is_send', 1);
            $bid_lists = $ProductBid->where(array('product_id'=>$value['product_id']))->group('user_id')->select();
            $data = array(
                'touser' => '',
                'msgtype' => 'news',
                'news' => array(
                    'articles' => array(
                        array(
                            'title' => '5分钟结拍提示',
                            'description' => '你好，【'. $value['product_name'] .'】还有5分钟结束。'. $money . PHP_EOL . '结拍时间：' .  date('Y-m-d H:i:s', $value['end_time']),
                            'url' => C('WEB_SITE_URL') . U('Index/product', array('id'=>$value['product_id'])),
                            'picurl' => ''
                        )
                    )
                )
            );
            foreach ($bid_lists as $val) {
                $data['touser'] = get_shop_info($val['user_id'], 'openid');
                $this->WX->sendCustomMessage($data);
            }
        }

        /* 3分钟提示 */
        $map = array('product_type' => array('in', '1,2'), 'is_send' => 1, 'status' => 1, 'end_time' => array('elt', NOW_TIME + 180));
        $product_lists = $Product->where($map)->select();
        foreach ($product_lists as $value) {
			$Product->where(array('product_id'=>$value['product_id']))->setField('is_send', 2);
            $bid_lists = $ProductBid->where(array('product_id'=>$value['product_id']))->group('user_id')->select();
            $data = array(
                'touser' => '',
                'msgtype' => 'news',
                'news' => array(
                    'articles' => array(
                        array(
                            'title' => '3分钟结拍提示',
                            'description' => '你好，【'. $value['product_name'] .'】还有3分钟结束。'. $money . PHP_EOL . '结拍时间：' .  date('Y-m-d H:i:s', $value['end_time']),
                            'url' => C('WEB_SITE_URL') . U('Index/product', array('id'=>$value['product_id'])),
                            'picurl' => ''
                        )
                    )
                )
            );
            foreach ($bid_lists as $val) {
                $data['touser'] = get_shop_info($val['user_id'], 'openid');
                $this->WX->sendCustomMessage($data);
            }
        }

        /* 1分钟提示 */
        $map = array('product_type' => array('in', '1,2'), 'is_send' => 2, 'status' => 1, 'end_time' => array('elt', NOW_TIME + 60));
        $product_lists = $Product->where($map)->select();
        foreach ($product_lists as $value) {
			$Product->where(array('product_id'=>$value['product_id']))->setField('is_send', 3);
            $bid_lists = $ProductBid->where(array('product_id'=>$value['product_id']))->group('user_id')->select();
            $data = array(
                'touser' => get_shop_info($val['user_id'], 'openid'),
                'msgtype' => 'news',
                'news' => array(
                    'articles' => array(
                        array(
                            'title' => '1分钟结拍提示',
                            'description' => '你好，【'. $value['product_name'] .'】还有1分钟结束。'. $money . PHP_EOL . '结拍时间：' .  date('Y-m-d H:i:s', $value['end_time']),
                            'url' => C('WEB_SITE_URL') . U('Index/product', array('id'=>$value['product_id'])),
                            'picurl' => ''
                        )
                    )
                )
            );
            foreach ($bid_lists as $val) {
                $data['touser'] = get_shop_info($val['user_id'], 'openid');
                $this->WX->sendCustomMessage($data);
            }
        }

    }

    // 同步所有关注信息
    public function sync(){
        $user_list = $this->WX->getUserList();
        // echo '<pre>'; print_r($user_list);
        if ($user_list['total']) {
            $map = array(
                'openid' => array('in', $user_list['data']['openid'])
            );
            $User = M('User');
            $User->where($map)->setField('is_subscribe', 1);
            // echo $User->_sql();
            $map = array(
                'openid' => array('not in', $user_list['data']['openid'])
            );
            $User->where($map)->setField('is_subscribe', 0);
            // echo $User->_sql();
            $this->show('成功');
        }
    }

    // 自定义菜单更新
    public function zdy(){
        //设置菜单
        $newmenu =  array(
            "button"=>array(
                array(
                    'type'=>'view',
                    'name'=>'拍卖现场',
                    'sub_button' => array(
                        array('type'=>'view','name'=>'进入拍场','url'=>C('WEB_SITE_URL')),
                        array('type'=>'view','name'=>'社区互动','url'=>C('WEB_SITE_URL').'/bbs/'),
                    )
                ),
                array(
                    'type'=>'view',
                    'name'=>'发现',
                    'url'=> C('WEB_SITE_URL') . U('Index/faxian')
                ),
                array(
                    'type'=>'view',
                    'name'=>'招商加盟',
                    'url'=> 'http://mp.weixin.qq.com/s?__biz=MzI2OTA4MzI4NA==&mid=401275519&idx=1&sn=0530fff2456e6501f4100e776d947f31&scene=0#wechat_redirect'
                ),
                // array(
                    // 'name'=>'客户服务',
                    // 'sub_button' => array(
                        // array('type'=>'click','name'=>'人工服务','key'=>'MENU_KEY_NEWS'),
                        // array('type'=>'view','name'=>'新手必读','url'=>C('WEB_SITE_URL')),
                        // array('type'=>'view','name'=>'中拍商品','url'=>C('WEB_SITE_URL')),
                    // )
                // ),
            )
        );
        $result = $this->WX->createMenu($newmenu);
        if ($result) {
            exit('成功');
        } else {
            exit('失败');
        }
    }

     /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     * 在mp.weixin.qq.com 开发者中心配置的 URL(服务器地址)  http://域名/index.php/home/weixin/index/id/member_public表的id.html
     */
	public function index() {
        $this->WX->valid();
        $type = $this->WX->getRev()->getRevType();
        if (C('DEVELOP_MODE')) {
            addWeixinLog($GLOBALS ['HTTP_RAW_POST_DATA']);
        }
        //与微信交互的中控服务器逻辑可以自己定义，这里实现一个通用的
        switch ($type) {
            //事件
            case TPWechat::MSGTYPE_EVENT:         //先处理事件型消息
                $event = $this->WX->getRevEvent();
                switch ($event['event']) {
                    //关注l
                    case TPWechat::EVENT_SUBSCRIBE:
                        //二维码关注
                        if(isset($event['eventkey']) && isset($event['ticket'])){

                        }else{
                            //普通关注
                        }
                        $openid = $this->WX->getRevFrom();
                        $User = M('User');
                        $user_info = $User->where(array('openid'=>$openid))->find();
                        if ($user_info) {
                            $map = array(
                                'openid' => $this->WX->getRevFrom()
                            );
                            $User->where($map)->setField('is_subscribe', 1);
                        } else {
                            $wx_info = $this->WX->getUserInfo($this->WX->getRevFrom());
                            $wx_info['nickname'] = remove_emoji($wx_info['nickname']);
                            $data = array(
                                'openid' => $openid,
                                'user_name' => $wx_info['nickname'],
                                'user_avatar' => $wx_info['headimgurl'],
                                'is_subscribe' => 1,
                                'status' => 1,
                                'create_time' => NOW_TIME,
                                'update_time' => NOW_TIME
                            );
                            $User->add($data);
                        }
                        $this->WX->text(html_entity_decode(C('SUBSCRIBE_MSG')))->reply();

                        break;

                    //扫描二维码
                    case TPWechat::EVENT_SCAN:

                        break;
                    //地理位置
                    case TPWechat::EVENT_LOCATION:

                        break;
                    //自定义菜单 - 点击菜单拉取消息时的事件推送
                    case TPWechat::EVENT_MENU_CLICK:
                        switch ($event['key']) {
                            case 'MENU_KEY_NEWS':

                                break;

                            default:
                                # code...
                                break;
                        }

                        break;
                    //自定义菜单 - 点击菜单跳转链接时的事件推送
                    case TPWechat::EVENT_MENU_VIEW:
                        $this->WX->text('//自定义菜单 - 点击菜单跳转链接时的事件推送')->reply();
                        break;
                    //自定义菜单 - 扫码推事件的事件推送
                    case TPWechat::EVENT_MENU_SCAN_PUSH:

                        break;
                    //自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
                    case TPWechat::EVENT_MENU_SCAN_WAITMSG:

                        break;
                    //自定义菜单 - 弹出系统拍照发图的事件推送
                    case TPWechat::EVENT_MENU_PIC_SYS:

                        break;
                    //自定义菜单 - 弹出拍照或者相册发图的事件推送
                    case TPWechat::EVENT_MENU_PIC_PHOTO:

                        break;
                    //自定义菜单 - 弹出微信相册发图器的事件推送
                    case TPWechat::EVENT_MENU_PIC_WEIXIN:

                        break;
                    //自定义菜单 - 弹出地理位置选择器的事件推送
                    case TPWechat::EVENT_MENU_LOCATION:

                        break;
                    //取消关注
                    case TPWechat::EVENT_UNSUBSCRIBE:
                        $openid = $this->WX->getRevFrom();
                        $User = M('User');
                        $user_info = $User->where(array('openid'=>$openid))->find();
                        if ($user_info) {
                            $map = array(
                                'openid' => $this->WX->getRevFrom()
                            );
                            $User->where($map)->setField('is_subscribe', 0);
                        } else {
                            $wx_info = $this->WX->getUserInfo($this->WX->getRevFrom());
                            $data = array(
                                'openid' => $openid,
                                'user_name' => $wx_info['nickname'],
                                'user_avatar' => $wx_info['headimgurl'],
                                'is_subscribe' => 0,
                                'status' => 1,
                                'create_time' => NOW_TIME,
                                'update_time' => NOW_TIME
                            );
                            $User->add($data);
                        }
                        break;
                    //群发接口完成后推送的结果
                    case TPWechat::EVENT_SEND_MASS:

                        break;
                    //模板消息完成后推送的结果
                    case TPWechat::EVENT_SEND_TEMPLATE:

                        break;
                    default:

                        break;
                }
                break;
            //文本
            case TPWechat::MSGTYPE_TEXT :
                $keyword = trim($this->WX->getRevContent());

                switch ($keyword) {
                    case '1':
                        $this->WX->text(html_entity_decode(C('SUBSCRIBE_MSG')))->reply();
                        break;

                    case '2':
                        $data = array(
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description1',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/3_G_1440030087739.jpg',
                                'Url' => ''
                            ),
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description2',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/5_G_1440028482068.jpg',
                                'Url' => ''
                            ),
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description3',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/5_G_1440028482068.jpg',
                                'Url' => ''
                            ),
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description4',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/5_G_1440028482068.jpg',
                                'Url' => ''
                            )
                        );
                        $this->WX->news($data)->reply();
                        break;

                    default:
                        //$this->WX->text("默认信息...")->reply();
						//接入客服
						$this->WX->transfer_customer_service()->reply();
                        break;
                }
                break;
            //图像
            case TPWechat::MSGTYPE_IMAGE :

                break;
            //语音
            case TPWechat::MSGTYPE_VOICE :

                break;
            //视频
            case TPWechat::MSGTYPE_VIDEO :

                break;
            //位置
            case TPWechat::MSGTYPE_LOCATION :

                break;
            //链接
            case TPWechat::MSGTYPE_LINK :

                break;
            default:

                break;
        }
	}

    // 销毁
    public function _destructor(){

    }
}
