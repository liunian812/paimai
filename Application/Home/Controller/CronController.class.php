<?php
namespace Home\Controller;
use Home\Common\IController;
use Vendor\TPWechat;
class CronController extends IController {
    protected function _init() {}
    protected function _cron() {}
    private function _run(){
        G('begin');
        if (file_exists(WEB_ROOT . '/cron-0')) {
            return;
        } else {
            file_put_contents(WEB_ROOT . '/cron-0', 'cron-0');
        }
        /* 订单处理 */
        $Order = M('Order');
        $map = array('end_time'=>array('elt', NOW_TIME), 'status'=>array('in','1,2,3'));
        $order_lsits = $Order->where($map)->select();
        foreach ($order_lsits as $key => $val) {
            $price = $val['price'] + $val['postage'];
            $product_name = get_product_info($val['product_id']);
            switch ($val['status']) {
                case '1':   //未付款
                    $parm = array(
                        'status' => 5,
                        'update_time' => $val['end_time'],
                        'desc' => '买家违约'
                    );
                    $data = array(
                        'first' => '你好，【'. $product_name .'】订单已经关闭，买家没有付款。',
                        'order_id' => $val['order_id'],
                        'order_status' => '已关闭（买家违约）',
                        'product_name' => $product_name,
                        'order_price' => $val['price'] + $val['postage'],
                        'end_time' => '违约时间：' . date('Y-m-d H:i:s', $val['end_time'])
                    );
                    break;
                case '2':   //未发货
                    $parm = array(
                        'status' => 5,
                        'update_time' => $val['end_time'],
                        'desc' => '卖家违约'
                    );
                    $data = array(
                        'first' => '你好，【'. $product_name .'】订单已经关闭，卖家没有发货，买家支付款将退还给买家',
                        'order_id' => $val['order_id'],
                        'order_status' => '已关闭（卖家违约）',
                        'product_name' => $product_name,
                        'order_price' => $price,
                        'end_time' => '违约时间：' . date('Y-m-d H:i:s', $val['end_time'])
                    );
                    // 退还买家支付款
                    fund_inc($val['user_id'], $price, '支付款退还');
                    break;
                case '3':   //系统自动签收
                    $parm = array(
                        'status' => 4,
                        'update_time' => $val['end_time'],
                        'desc' => '自动签收'
                    );
                    $data = array(
                        'first' => '你好，【'. $product_name .'】订单已经完成，系统自动签收。',
                        'order_id' => $val['order_id'],
                        'order_status' => '已完成（自动签收）',
                        'product_name' => $product_name,
                        'order_price' => $price,
                        'end_time' => '完成时间：' . date('Y-m-d H:i:s', $val['end_time'])
                    );
					// 为商家充值
					fund_inc($val['shop_id'], $price, '买卖交易');
                    break;
                default:
                    break;
            }
            // 更新订单状态
            $Order->where(array('order_id'=>$val['order_id']))->save($parm);
            // 给卖家发
            $this->sendOrder(get_shop_info($val['shop_id'], 'openid'), U('Order/index', array('order_id'=>$val['order_id'])), $data);
            // 给买家发
            $this->sendOrder(get_shop_info($val['user_id'], 'openid'), U('Order/index', array('order_id'=>$val['order_id'])), $data);
        }

        /* 产品处理 */
        $Product = M('Product');
        $ProductBid = M('ProductBid');
        $map = array('status' => 1, 'end_time' => array('elt', NOW_TIME));
        $product_lists = $Product->where($map)->select();
        foreach ($product_lists as $key => $val) {
            switch ($val['product_type']) {
                case '1':   //普通
                case '2':   //竞速
                    $bid_info = $ProductBid->where(array('product_id'=>$val['product_id']))->order('bid_price desc')->find();
                    if ($bid_info) {
                        if ($bid_info['bid_price'] >= $val['reserve_price']) {
                            //改变状态
                            $result = $Product->where(array('product_id'=>$val['product_id']))->setField('status', 2);
                            $count = $Order->where(array('product_id'=>$val['product_id']))->count();
                            if ($result && empty($count)) {
                                // 下单
                                $order_id = create_order_id();
                                $end_time = strtotime('+2 day');
                                $data = array(
                                    'order_id' => $order_id,
                                    'shop_id' => $val['user_id'],
                                    'user_id' => $bid_info['user_id'],
                                    'product_id' => $val['product_id'],
                                    'price' => $bid_info['bid_price'],
                                    'postage' => $val['postage'],
                                    'create_time' => NOW_TIME,
                                    'update_time' => NOW_TIME,
                                    'end_time' => $end_time,
                                    'status' => 1
                                );
                                M('Order')->add($data);

                                // 给卖家发
                                $data = array(
                                    'first' => '你好，【'. $val['product_name'] .'】已经自动结拍，订单已经生成。',
                                    'order_id' => $order_id,
                                    'order_status' => '待支付（等待买家支付）',
                                    'product_name' => $val['product_name'],
                                    'order_price' => $bid_info['bid_price'] + $val['postage'],
                                    'end_time' => '违约有效期：' . date('Y-m-d H:i:s', $end_time),
                                );
                                $this->sendOrder(get_shop_info($val['user_id'], 'openid'), U('Order/index', array('order_id'=>$order_id)), $data);
                                // 给买家发
                                $data['first'] = '你好，【'. $val['product_name'] .'】已经自动结拍，订单已经生成，请在付款有效期内付款。';
                                $this->sendOrder(get_shop_info($bid_info['user_id'], 'openid'), U('Order/index', array('order_id'=>$order_id)), $data);
                            }
                        } else {
                            // 给卖家发
                            $data = array(
                                'first' => '你好，【'. $val['product_name'] .'】已经流拍，出价没有高于或等于保留价。',
                                'product_id' => $val['product_id'],
                                'product_name' => $val['product_name'],
                                'remark' => '流拍时间：' . date('Y-m-d H:i:s', $val['end_time'])
                            );
                            $this->sendBid(get_shop_info($val['user_id'], 'openid'), U('Index/product', array('id'=>$val['product_id'])), $data);
                            $Product->where(array('product_id'=>$val['product_id']))->setField('status', 3);
                        }
                    } else {
                        // 给卖家发
                        $data = array(
                            'first' => '你好，【'. $val['product_name'] .'】已经流拍，没有人出价。',
                            'product_id' => $val['product_id'],
                            'product_name' => $val['product_name'],
                            'remark' => '流拍时间：' . date('Y-m-d H:i:s', $val['end_time'])
                        );
                        $this->sendBid(get_shop_info($val['user_id'], 'openid'), U('Index/product', array('id'=>$val['product_id'])), $data);
                        $Product->where(array('product_id'=>$val['product_id']))->setField('status', 3);
                    }
                    break;
                case '3':   //秒杀
                    $Product->where(array('product_id'=>$val['product_id']))->setField('status', 2);
                    break;
                default:
                    # code...
                    break;
            }
        }

        /* 5分钟提示 */
        $map = array('product_type' => array('in', '1,2'), 'is_send' => 0, 'status' => 1, 'end_time' => array('elt', NOW_TIME + 300));
        $product_lists = $Product->where($map)->select();
        foreach ($product_lists as $value) {
			$Product->where(array('product_id'=>$value['product_id']))->setField('is_send', 1);
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
        G('end');
        @unlink(WEB_ROOT . '/cron-0');
        // file_put_contents(WEB_ROOT .'/cron-log.txt', date('Y-m-d H:i:s') . PHP_EOL . G('begin','end') . '0s' . PHP_EOL, FILE_APPEND);
    }
    // 脚本执行
    public function index(){
        ignore_user_abort(true);
        set_time_limit(0);

        for ($i=0; $i < 60; $i++) {
            $this->_run();
            sleep(1);
        }
        // $data = array(
        //     'touser' => 'olEDawig2ZbRO2v2zBNoyxXB32SE',
        //     'msgtype' => 'news',
        //     'news' => array(
        //         'articles' => array(
        //             array(
        //                 'title' => '定时执行提示',
        //                 'description' => '执行时间：' .  date('Y-m-d H:i:s'),
        //                 'url' => C('WEB_SITE_URL'),
        //                 'picurl' => ''
        //             )
        //         )
        //     )
        // );
        // $this->WX->sendCustomMessage($data);
        exit('结束'. PHP_EOL);
    }

    public function test(){
        print_r($this->WX->checkAuth());
    }
    // 开始
    public function start(){
        file_put_contents(WEB_ROOT .'/cron-switch', 'cron-switch');
        $this->_sock(WEB_URL . U('index'));
        $this->success('成功');
    }

    // 结束
    public function end(){
        @unlink(WEB_ROOT .'/cron-switch');
		sleep(1);
        $this->success('成功');
    }

    public function status(){
        if(file_exists(WEB_ROOT .'/cron-run')){
            $this->success('运行中');
        } else {
            $this->error('关闭中');
        }
    }

    // 远程请求（不获取内容）函数，下面可以反复使用
    private function _sock($url) {
    	$host = parse_url($url,PHP_URL_HOST);
    	$port = parse_url($url,PHP_URL_PORT);
    	$port = $port ? $port : 80;
    	$scheme = parse_url($url,PHP_URL_SCHEME);
    	$path = parse_url($url,PHP_URL_PATH);
    	$query = parse_url($url,PHP_URL_QUERY);
    	if($query) $path .= '?'.$query;
    	if($scheme == 'https') {
    		$host = 'ssl://'.$host;
    	}
    	$fp = fsockopen($host,$port,$error_code,$error_msg,1);
    	if (!$fp) {
    		return array('error_code' => $error_code,'error_msg' => $error_msg);
    	} else {
    		stream_set_blocking($fp,true);//开启了手册上说的非阻塞模式
    		stream_set_timeout($fp,1);//设置超时
    		$header = "GET $path HTTP/1.1\r\n";
    		$header.="Host: $host\r\n";
    		$header.="Connection: close\r\n\r\n";//长连接关闭
    		fwrite($fp, $header);
    		usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
    		fclose($fp);
    		return array('error_code' => 0);
    	}
    }

    // 销毁
    public function _destructor(){

    }
}
