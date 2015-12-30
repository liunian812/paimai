<?php
namespace Home\Controller;
use Home\Common\IController;
class OrderController extends IController {

    public function pay(){
        // addressId:6
        // orderCode:NS201511181100519199
        // tradeMethod:2600
        $address_id = I('addressId', 0, 'intval');
        $order_id = I('orderCode');
        $tradeMethod = I('tradeMethod');
        if (empty($address_id)) {
            $this->resultError('请添加收货地址');
        }
        switch ($tradeMethod) {
            case '2600':
                $map = array(
                    'user_id' => $this->user_info['user_id'],
                    'order_id' => $order_id
                );
                $data = array(
                    'update_time' => NOW_TIME,
                    'address_id' => $address_id
                );
                $result = M('Order')->where($map)->save($data);
                if ($result) {
                    $this->resultSuccess('微信支付', '', U('WxJsApi/index', array('order_id'=>$order_id)));
                } else {
                    $this->resultError('网络异常...');
                }

                break;

            default:
                $this->resultError('选择支付方式...');
                break;
        }


    }

    public function index($order_id = ''){
        $order_info = M('Order')->find($order_id);
        if (!$order_info) {
            $this->redirect('User/index');
        }
        if ($order_info['shop_id'] == $this->user_info['user_id']) {
            switch ($order_info['status']) {
                case '1':

                    break;
                case '2':
                case '3':
                case '4':
                        // 获取收货地址
                        $consign_info = M('UserConsign')->find($order_info['address_id']);
                        $consign_info['area'] = json_decode($consign_info['area'], true);
                        $this->assign('consign_info', $consign_info);
                    break;
                default:
                    # code...
                    break;
            }
            $order_info['shop_info'] = get_shop_info($order_info['shop_id'], '');
            $order_info['user_info'] = get_shop_info($order_info['user_id'], '');
            $order_info['product_info'] = get_product_info($order_info['product_id'], '');
            $order_info['z_price'] =$order_info['product_info']['postage'] + $order_info['price'];
            $this->assign('order_info', $order_info);

            $this->display('indexShop');
        } elseif ($order_info['user_id'] == $this->user_info['user_id']){
            switch ($order_info['status']) {
                case '1':
                        // 获取收货地址
                        $address_id = I('address_id');
                        if ($address_id) {
                            $consign_info = M('UserConsign')->find($address_id);
                            $consign_info['area'] = json_decode($consign_info['area'], true);
                        } else {
                            $consign_info = default_consign($this->user_info['user_id']);
                        }
                        // print_r($consign_info);
                        $this->assign('consign_info', $consign_info);
                    break;
                case '2':
                case '3':
                case '4':
                        // 获取收货地址
                        $consign_info = M('UserConsign')->find($order_info['address_id']);
                        $consign_info['area'] = json_decode($consign_info['area'], true);
                        $this->assign('consign_info', $consign_info);
                    break;
                default:
                    # code...
                    break;
            }

            $order_info['shop_info'] = get_shop_info($order_info['shop_id'], '');
            $order_info['product_info'] = get_product_info($order_info['product_id'], '');
            $order_info['z_price'] =$order_info['product_info']['postage'] + $order_info['price'];
            $this->assign('order_info', $order_info);
            $this->display('indexUser');
        } else {
            $this->redirect('User/index');
        }
    }
    // 发货
    public function faHuo(){
        $order_id = I('order_id');
        if (empty($order_id)) {
            $this->resultError('订单号不能为空');
        }
        $kuaidi_gs = I('kuaidi_gs');
        if (empty($kuaidi_gs)) {
            $this->resultError('快递公司不能为空');
        }
        $kuaidi_nu = I('kuaidi_nu');
        if (empty($kuaidi_nu)) {
            $this->resultError('快递单号不能为空');
        }
        $order_info = M('Order')->find($order_id);
        if (!$order_info) {
            $this->resultError('订单不存在...');
        }
        $map = array(
            'shop_id' => $this->user_info['user_id'],
            'order_id' => $order_id
        );
        $end_time = strtotime('+15 day');
        $data = array(
            'kuaidi_gs' => $kuaidi_gs,
            'kuaidi_nu' => $kuaidi_nu,
            'status' => 3,
			'update_time' => NOW_TIME,
            'end_time' => $end_time
        );
        $result = M('Order')->where($map)->save($data);
        if ($result) {
            // 给买家发
            $order_info['product_name'] = get_product_info($order_info['product_id']);
            $data = array(
                'first' => '你好，【'. $order_info['product_name'] .'】订单已经发货，请在有效期内签收。',
                'order_id' => $order_id,
                'order_status' => '已发货（等待买家签收）',
                'product_name' => $order_info['product_name'],
                'end_time' => '系统自动签收时间：' . date('Y-m-d H:i:s', $end_time),
            );
            $this->sendOrder(get_shop_info($order_info['user_id'], 'openid'), U('Order/index', array('order_id'=>$order_id)), $data);
            $this->resultSuccess('发货成功', '', U('Order/index', array('order_id'=>$order_id)));
        } else {
            $this->resultError('网络异常...');
        }

    }
    // 签收
    public function qianShou(){
        $order_id = I('order_id');
        if (empty($order_id)) {
            $this->resultError('订单号不能为空');
        }
        $order_info = M('Order')->find($order_id);
        if (!$order_info) {
            $this->resultError('订单不存在...');
        }
        $map = array(
            'user_id' => $this->user_info['user_id'],
            'order_id' => $order_id
        );
        $data = array(
            'status' => 4,
            'update_time' => NOW_TIME
        );
        $result = M('Order')->where($map)->save($data);
        if ($result) {
            // 给卖家发
            $order_info['product_name'] = get_product_info($order_info['product_id']);
            $data = array(
                'first' => '你好，【'. $order_info['product_name'] .'】订单已经签收，订单顺利完成。',
                'order_id' => $order_id,
                'order_status' => '已签收（订单完成）',
                'product_name' => $order_info['product_name'],
                'end_time' => '签收时间：' . date('Y-m-d H:i:s'),
            );
            $this->sendOrder(get_shop_info($order_info['user_id'], 'openid'), U('Order/index', array('order_id'=>$order_id)), $data);
            $this->sendOrder(get_shop_info($order_info['shop_id'], 'openid'), U('Order/index', array('order_id'=>$order_id)), $data);
            // 为商家充值
            fund_inc($order_info['shop_id'], $order_info['price'] + $order_info['postage'], '买卖交易');
            $this->resultSuccess('签收成功', '', U('Order/index', array('order_id'=>$order_id)));
        } else {
            $this->resultError('网络异常...');
        }

    }

    /* 卖出订单 */
    public function shop($status = 1){
        if (IS_AJAX) {
            $page_size = 10;

            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $map = array(
                'shop_id' => $this->user_info['user_id'],
                'status' => $status
            );

            $Order = M('Order');
            $total_count = $Order->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $order_list = $Order->where($map)->order('create_time desc')->limit($start .','. $page_size)->select();
                foreach ($order_list as $key => $value) {
                    $order_list[$key]['shop_info'] = get_shop_info($value['shop_id'], '');
                    $order_list[$key]['product_info'] = get_product_info($value['product_id'], '');
                    $order_list[$key]['z_price'] =$order_list[$key]['product_info']['postage'] + $value['price'];
                }
                $this->assign('order_list', $order_list);
                // print_r($order_list); exit;
                $content = $this->fetch('ajaxShop');
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
            $this->assign('status', $status);
            $this->display();
        }
    }

    /* 买入订单 */
    public function user($status = 1){
        if (IS_AJAX) {
            $page_size = 10;

            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $map = array(
                'user_id' => $this->user_info['user_id'],
                'status' => $status
            );

            $Order = M('Order');
            $total_count = $Order->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $order_list = $Order->where($map)->order('create_time desc')->limit($start .','. $page_size)->select();
                foreach ($order_list as $key => $value) {
                    $order_list[$key]['shop_info'] = get_shop_info($value['shop_id'], '');
                    $order_list[$key]['product_info'] = get_product_info($value['product_id'], '');
                    $order_list[$key]['z_price'] =$order_list[$key]['product_info']['postage'] + $value['price'];
                }
                $this->assign('order_list', $order_list);
                // print_r($order_list); exit;
                $content = $this->fetch('ajaxUser');
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
            $this->assign('status', $status);
            $this->display();
        }
    }


}
