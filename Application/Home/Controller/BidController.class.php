<?php
namespace Home\Controller;
use Home\Common\IController;
class BidController extends IController {
    // 我的出价
    public function index($type = 'sale'){
        $this->assign('type', $type);
        if (IS_AJAX) {
            $page_size = 10;
            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            switch ($type) {
                case 'sale':
                    $map = array(
                        'user_id' => $this->user_info['user_id'],
                        'begin_time' => array('lt', NOW_TIME),
                        'end_time' => array('gt', NOW_TIME),
                        'status' => 1
                    );
                    break;
                case 'end':
                    $map = array(
                        'user_id' => $this->user_info['user_id'],
                        'begin_time' => array('lt', NOW_TIME),
                        'end_time' => array('lt', NOW_TIME),
                        'status' => array('egt', 1)
                    );
                    break;
                default:
                    # code...
                    break;
            }
            $ProductBid = D('ProductBidView');
            $total_count = $ProductBid->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $bid_list = $ProductBid->where($map)->order('bid_id desc')->limit($start .','. $page_size)->select();
                foreach ($bid_list as $key => $value) {
                    $bid_list[$key]['product_img'] = array_shift(json_decode($value['product_images'], true));
                }
                $this->assign('bid_list', $bid_list);
                $this->assign('product_type', C('PRODUCT_TYPE'));
                $content = $this->fetch('ajaxIndex');
                $value = array(
                    'htmlString' => $content,
                    'isNextPage' => ($total_page == 1 || $total_page == $page) ? false : true,
                    'page' => $page,
                    'pageSize' => $page_size,
                    'totalCount' => $total_count,
                    'totalPage' => $total_page
                );
            } else {
                $value = array(
                    'htmlString' => '<div class="ui-section no-item" id="no-item">暂无数据</div>',
                    'isNextPage' => false,
                    'page' => $page,
                    'pageSize' => $page_size,
                    'totalCount' => $total_count,
                    'totalPage' => 1
                );
            }
            $this->resultSuccess('加载中...', $value);
        } else {
            $this->display();
        }
    }
    // 出价列表
    public function lists($productId){
        if (IS_AJAX) {
            $page_size = 10;
            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $map = array(
                'product_id' => $productId,
                'status' => 1
            );
            $ProductBid = M('ProductBid');
            $total_count = $ProductBid->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $bid_list = $ProductBid->where($map)->order('bid_id desc')->limit($start .','. $page_size)->select();
                $User = M('User');
                foreach ($bid_list as $key => $value) {
                    $bid_list[$key]['user_info'] = $User->find($value['user_id']);
                }
                $this->assign('bid_list', $bid_list);
                $product_info = M('Product')->where($map)->find();
                $this->assign('product_info', $product_info);
                $range_price = $product_info['range_price'];
                $content = $this->fetch('ajaxLists');
                if ($product_info['product_type'] == 3) {
                    $value = array(
                        'info' => array(
                            'currentPrice' => $product_info['range_price'],
                            'bidPrice' => $product_info['range_price'],
                            'lastId' => 1
                        ),
                        'htmlString' => $content,
                        'isNextPage' => ($total_page == 1 || $total_page == $page) ? false : true,
                        'page' => $page,
                        'pageSize' => $page_size,
                        'totalCount' => $total_count,
                        'totalPage' => $total_page
                    );
                } else {
                    $max_bid_price = $ProductBid->where($map)->max('bid_price');
                    $value = array(
                        'info' => array(
                            'currentPrice' => $max_bid_price,
                            'bidPrice' => $max_bid_price + $range_price,
                            'lastId' => 1
                        ),
                        'htmlString' => $content,
                        'isNextPage' => ($total_page == 1 || $total_page == $page) ? false : true,
                        'page' => $page,
                        'pageSize' => $page_size,
                        'totalCount' => $total_count,
                        'totalPage' => $total_page
                    );
                }
            } else {
                $value = array(
                    'htmlString' => '',
                    'isNextPage' => false,
                    'page' => $page,
                    'pageSize' => $page_size,
                    'totalCount' => $total_count,
                    'totalPage' => 1
                );
            }
            $this->resultSuccess('加载中...', $value);
        } else {
            //$this->display();
        }
    }

    // 加价
    public function add($productId, $price){
        if (!$this->user_info['is_subscribe']) {
            $this->resultError('请先关注公众平台【'. C('WEB_SITE_TITLE') .'】', '', 'http://mp.weixin.qq.com/s?__biz=MzI2OTA4MzI4NA==&mid=401275684&idx=4&sn=215571f800f39c82095f7dca83728a0e#rd');
        }
        if (!$this->user_info['mobile']) {
            $this->resultError('请先完善个人资料手机号参数必填', '', U('Setting/index'));
        }
        $product_info = M('Product')->find($productId);
        // 拍品是否存在
        if (!$product_info) {
            $this->resultError('拍品不存在...');
        }
        if ($product_info['user_id'] == $this->user_info['user_id']) {
            $this->resultError('不可以自娱自乐...');
        }
        // 判断类型
        switch ($product_info['product_type']) {
            case '1': //加价
            case '2': //竞速
                // 是否在拍卖
                if ($product_info['begin_time'] > NOW_TIME) {
                    $this->resultError('拍卖未开始');
                }
                if (NOW_TIME > $product_info['end_time']) {
                    $this->resultError('拍卖已结束');
                }
                // 拍品不在可出价状态
                if ($product_info['status'] != 1) {
                    $this->resultError('拍品不在可出价状态');
                }
                // 检查当前用户是否出价最高
                if (check_user_bid($productId, $this->user_info['user_id'])) {
                    $this->resultError('出价已经最高');
                }
                // 最低出价
                // $bid_price = max_bid_price($productId);
                $product_info['bid_info'] = M('ProductBid')->where(array('product_id'=>$product_info['product_id']))->order('bid_price desc')->find();
                if ($product_info['bid_info']) {
                    $xz_bid = $product_info['bid_info']['bid_price'] + $product_info['range_price'];
                } else {
                    $xz_bid = $product_info['start_price'];
                }
                if ($xz_bid > $price) {
                    $this->resultError('最低加价'. $xz_bid);
                }
                // 出价
                $data = array(
                    'product_id' => $product_info['product_id'],
                    'user_id' => $this->user_info['user_id'],
                    'bid_price' => $price,
                    'create_time' => NOW_TIME,
                    'update_time' => NOW_TIME,
                    'status' => 1
                );
                $result = M('ProductBid')->add($data);
                if (!$result) {
                    $this->resultError();
                }
                if ($product_info['reserve_price'] && ($price >= $product_info['reserve_price'])) {
                    // 下单
                    $order_id = create_order_id();
                    $end_time = strtotime('+2 day');
                    $data = array(
                        'order_id' => $order_id,
                        'shop_id' => $product_info['user_id'],
                        'user_id' => $this->user_info['user_id'],
                        'product_id' => $product_info['product_id'],
                        'price' => $price,
                        'postage' => $product_info['postage'],
                        'create_time' => NOW_TIME,
                        'update_time' => NOW_TIME,
                        'end_time' => $end_time,
                        'status' => 1
                    );
                    M('Order')->add($data);
                    $Product->where(array('product_id'=>$product_info['product_id']))->setField('status', 2);
                    // 给卖家发
                    $data = array(
                        'first' => '你好，【'. $product_info['product_name'] .'】已经结拍，订单已经生成。',
                        'order_id' => $order_id,
                        'order_status' => '待支付（等待买家支付）',
                        'product_name' => $product_info['product_name'],
                        'order_price' => $price + $product_info['postage'],
                        'end_time' => '违约有效期：' . date('Y-m-d H:i:s', $end_time),
                    );
                    $this->sendOrder(get_shop_info($product_info['user_id'], 'openid'), U('Order/index', array('order_id'=>$order_id)), $data);
                    // 给买家发
                    $data['first'] = '你好，【'. $product_info['product_name'] .'】已经结拍，订单已经生成，请在付款有效期内付款。';
                    $this->sendOrder($this->user_info['openid'], U('Order/index', array('order_id'=>$order_id)), $data);
                    // 通知买家
                    if ($product_info['bid_info']) {
                        $data = array(
                            'first' => '您的出价已被超越，【'. $this->user_info['user_name'] .'】出价' . $price .'元，已经得拍',
                            'product_id' => $product_info['product_id'],
                            'product_name' => $product_info['product_name'],
                            'remark' => '结拍时间：'. date('Y年m月d日 H:i:s', $product_info['end_time']),
                        );
                        $this->sendBid(get_shop_info($product_info['bid_info']['user_id'], 'openid'), U('Index/product', array('id'=>$product_info['product_id'])), $data);
                    }
                    $this->resultSuccess('出价得拍');
                } else {
                    // 如果在结拍30内出价会延时5，3，1分
                    if (($product_info['end_time'] - NOW_TIME) < 30) {
                        $product_ys_num = F('product_'. $product_info['product_id']);
                        switch ($product_ys_num) {
                            case '1':
                                $product_info['end_time'] += 180;
                                F('product_'. $product_info['product_id'], 2);
                                M('Product')->where(array('product_id'=>$product_info['product_id']))->setField('end_time', $product_info['end_time']);
                                break;
                            case '2':
                                $product_info['end_time'] += 60;
                                F('product_'. $product_info['product_id'], 3);
                                M('Product')->where(array('product_id'=>$product_info['product_id']))->setField('end_time', $product_info['end_time']);
                                break;
                            case '3':
                                // TODO:
                                break;
                            default:
                                $product_info['end_time'] += 300;
                                F('product_'. $product_info['product_id'], 1);
                                M('Product')->where(array('product_id'=>$product_info['product_id']))->setField('end_time', $product_info['end_time']);
                                break;
                        }
                    }
                    // 通知买家
                    if ($product_info['bid_info']) {
                        $data = array(
                            'first' => '您的出价已被超越，【'. $this->user_info['user_name'] .'】出价' . $price .'元，目前领先',
                            'product_id' => $product_info['product_id'],
                            'product_name' => $product_info['product_name'],
                            'remark' => '提醒：'. date('Y年m月d日 H:i:s', $product_info['end_time']) . '拍卖结束',
                        );
                        $this->sendBid(get_shop_info($product_info['bid_info']['user_id'], 'openid'), U('Index/product', array('id'=>$product_info['product_id'])), $data);
                    }
                    // 通知店铺
                    $data = array(
                        'first' => '您的拍品，【'. $this->user_info['user_name'] .'】出价' . $price .'元，目前领先',
                        'product_id' => $product_info['product_id'],
                        'product_name' => $product_info['product_name'],
                        'remark' => '提醒：'. date('Y年m月d日 H:i:s', $product_info['end_time']) . '拍卖结束',
                    );
                    $this->sendBid(get_shop_info($product_info['user_id'], 'openid'), U('Index/product', array('id'=>$product_info['product_id'])), $data);
                    $this->resultSuccess();
                }
                break;
            case '3': //秒杀
                // 是否在拍卖
                if ($product_info['begin_time'] > NOW_TIME) {
                    $this->resultError('秒杀未开始');
                }
                if (NOW_TIME > $product_info['end_time']) {
                    $this->resultError('秒杀已结束');
                }
                // 拍品不在可出价状态
                if ($product_info['status'] != 1) {
                    $this->resultError('拍品不在可出价状态');
                }
                // 检查当前用户是否已经秒杀
                $map = array(
                    'product_id' => $product_info['product_id'],
                    'user_id' => $this->user_info['user_id']
                );
                $is_miaosha = M('ProductBid')->where($map)->find();
                if ($is_miaosha) {
                    $this->resultError('已经秒杀过了');
                }
                // 拍品数量
                if ($product_info['reserve_price'] < 1) {
                    $this->resultError('秒杀数量有限下次早点来哦');
                }
                // 秒杀价
                if ($price < $product_info['range_price']) {
                    $this->resultError('最低秒杀价' . $product_info['range_price']);
                }
                $map = array('product_id' => $product_info['product_id']);
                $result = M('Product')->where($map)->setDec('reserve_price', 1);
                if (!$result) {
                    $this->resultError();
                }
                $data = array(
                    'product_id' => $product_info['product_id'],
                    'user_id' => $this->user_info['user_id'],
                    'bid_price' => $price,
                    'create_time' => NOW_TIME,
                    'update_time' => NOW_TIME,
                    'status' => 1
                );
                $result = M('ProductBid')->add($data);
                if (!$result) {
                    $this->resultError();
                }
                // 下单
                $order_id = create_order_id();
                $end_time = strtotime('+2 day');
                $data = array(
                    'order_id' => $order_id,
                    'shop_id' => $product_info['user_id'],
                    'user_id' => $this->user_info['user_id'],
                    'product_id' => $product_info['product_id'],
                    'price' => $price,
                    'postage' => $product_info['postage'],
                    'create_time' => NOW_TIME,
                    'update_time' => NOW_TIME,
                    'end_time' => $end_time,
                    'status' => 1
                );
                M('Order')->add($data);
                // 最后一个结束拍卖
                if ($product_info['reserve_price'] == 1) {
                    M('Product')->where(array('product_id'=>$product_info['product_id']))->setField('status', 2);
                }
                // 给卖家发
                $data = array(
                    'first' => '你好，【'. $product_info['product_name'] .'】秒杀拍品，有人秒杀抢购。',
                    'order_id' => $order_id,
                    'order_status' => '待支付（等待买家支付）',
                    'product_name' => $product_info['product_name'],
                    'order_price' => $price + $product_info['postage'],
                    'end_time' => '违约有效期：' . date('Y-m-d H:i:s', $end_time),
                );
                $this->sendOrder(get_shop_info($product_info['user_id'], 'openid'), U('Order/index', array('order_id'=>$order_id)), $data);
                // 给买家发
                $data['first'] = '你好，【'. $product_info['product_name'] .'】订单已经生成，请在付款有效期内付款。';
                $this->sendOrder($this->user_info['openid'], U('Order/index', array('order_id'=>$order_id)), $data);
                $this->resultSuccess();
                break;
            default:
                $this->resultError('参数错误...');
                break;
        }
    }

}
