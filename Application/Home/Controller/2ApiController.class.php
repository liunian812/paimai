<?php
namespace Home\Controller;
use Home\Common\IController;
use Vendor\TPWechat;
class ApiController extends IController {
    protected function _init() {}
    public function addTemplate(){
        $result = $this->WX->addTemplateMessage('TM00015');
        if ($result) {
            exit('成功<br />' . $result);
        } else {
            exit('失败');
        }
    }

    public function setTemplate(){
        $result = $this->WX->setTMIndustry(1,4);
        if ($result) {
            exit('成功<br />' . $result);
        } else {
            exit('失败');
        }
    }

    public function sendTemplate(){
        $data = '｛
       			"touser":"o4I-2uMfbRWV-KlcnujVL2JP8KZU",
       			"template_id":"scS8hYOMO9yGe_MNd54tBa9KspdKf57vzyRwfgYLJTY",
       			"url":"http://wwb.sypole.com/",
       			"topcolor":"#FF0000",
       			"data":{
       				"first": {
       					"value":"您好，您的出价已被超越",
       					"color":"#173177"
       					},
       				"keyword1":{
       					"value":"南红一串",
       					"color":"#173177"
       					},
       				"keyword2":{
       					"value":"200",
       					"color":"#173177"
       					},
       				"keyword3":{
       					"value":"300",
       					"color":"#173177"
       					},
       				"keyword4":{
       					"value":"2015年8月25日 18:39",
       					"color":"#173177"
       					},
       				"remark":{
       					"value":"快去看看吧，感谢您的关注",
       					"color":"#173177"
       					}
       			}
       		}';
        $result = $this->WX->sendTemplateMessage($data);
        if ($result) {
            exit('成功<br />' . $result);
        } else {
            exit('失败');
        }
    }

    public function zdy(){
        //设置菜单
        $newmenu =  array(
            "button"=>array(
                array(
                    'type'=>'view',
                    'name'=>'进入拍场',
                    'url'=>'http://wwb.sypole.com/'
                ),
                array(
                    'type'=>'view',
                    'name'=>'发现',
                    'url'=>'http://wwb.sypole.com/html/'
                ),
                array(
                    'name'=>'客户服务',
                    'sub_button' => array(
                        array('type'=>'click','name'=>'人工服务','key'=>'MENU_KEY_NEWS'),
                        array('type'=>'view','name'=>'新手必读','url'=>'http://wwb.sypole.com/html/'),
                        array('type'=>'view','name'=>'中拍商品','url'=>'http://wwb.sypole.com/html/'),
                    )
                ),
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
        addWeixinLog($GLOBALS ['HTTP_RAW_POST_DATA']);
        //与微信交互的中控服务器逻辑可以自己定义，这里实现一个通用的

        switch ($type) {
            //事件
            case TPWechat::MSGTYPE_EVENT:         //先处理事件型消息
                $event = $this->WX->getRevEvent();
                switch ($event['event']) {
                    //关注
                    case TPWechat::EVENT_SUBSCRIBE:
                        //二维码关注
                        if(isset($event['eventkey']) && isset($event['ticket'])){

                        }else{
                            //普通关注
                        }
                        $this->WX->text('[呲牙]感谢您关注微拍堂'. PHP_EOL . PHP_EOL
                        . '微拍堂：迅速搭建一个高大上的微拍系统，为您提供完美的拍卖体验' . PHP_EOL . PHP_EOL
                        . '<a href="http://wwb.sypole.com/">点击这里,查看操作步骤</a>')->reply();

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
                addWeixinLog($type);
                $keyword = trim($this->WX->getRevContent());

                switch ($keyword) {
                    case '1':
                        $this->WX->text('[呲牙]感谢您关注微拍堂'. PHP_EOL . PHP_EOL
                        . '微拍堂：迅速搭建一个高大上的微拍系统，为您提供完美的拍卖体验' . PHP_EOL . PHP_EOL
                        . '<a href="http://wwb.sypole.com/">点击这里,查看操作步骤</a>')->reply();
                        break;

                    case '2':
                        $data = array(
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description1',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/3_G_1440030087739.jpg',
                                'Url' => 'http://wwb.sypole.com/'
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
                        $this->WX->text("默认信息...")->reply();
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



}
