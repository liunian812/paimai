<?php
namespace Home\Controller;
use Home\Common\IController;
class ShareController extends IController {



    public function index(){
        $UserQunfa = M('UserQunfa');
        $map = array(
            'user_id'       => $this->user_info['user_id'],
            'time'          => array('gt', strtotime(date('Ymd')))
        );
        $qunfa_list = $UserQunfa->where($map)->select();
        $qunfa = array();
        if ($qunfa_list) {
            foreach ($qunfa_list as $key => $value) {
                foreach (explode(',', $value['data']) as $v) {
                    $qunfa[] = $v;
                }
            }
        }
        $Product = M('Product');
        $map = array(
            'user_id'       => $this->user_info['user_id'],
            'update_time'   => array('gt', strtotime(date('Ymd'))),
            'status'        => 1,
        );
        $product_list = $Product->where($map)->select();
        $share_list = array();
        foreach ($product_list as $key => $value) {
            if (in_array($value['product_id'], $qunfa)) {
                unset($product_list[$key]);
                continue;
            }
            $product_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
            if ($value['is_share']) {
                $share_list[] = $value['product_id'];
            }
        }


        // echo $this->user_info['user_id'];
        // echo '<pre>'; print_r($product_list); exit;
        $this->assign('yifa_num', C('SHARE_QUNFA_NUM') - count($qunfa_list));
        $this->assign('share_list', json_encode($share_list));
        $this->assign('product_list', $product_list);
        $this->display();
    }

    public function send($saleIds){
        if (empty($saleIds)) {
            $this->resultError();
        }
        $UserQunfa = M('UserQunfa');
        $map = array(
            'user_id'       => $this->user_info['user_id'],
            'time'          => array('gt', strtotime(date('Ymd')))
        );
        $yifa_num = $UserQunfa->where($map)->count();
        if ($yifa_num >= C('SHARE_QUNFA_NUM')) {
            $this->resultError('每天最多群发'. C('SHARE_QUNFA_NUM') . '条');
        }
        $UserCollect = M('UserCollect');
        $fans_list = $UserCollect->where(array('shop'=>$this->user_info['user_id']))->select();
        if (empty($fans_list)) {
            $this->resultError('没有人收藏你的店铺');
        }

        $Product = M('Product');
        $map = array(
            'user_id'       => $this->user_info['user_id'],
            'product_id'   => array('in', $saleIds),
            'status'        => 1,
        );
        $product_list = $Product->where($map)->select();
        $data = array(
            'touser' => '',
            'msgtype' => 'news',
            'news' => array(
                'articles' => array()
            )
        );
        $faing = array();
        foreach ($product_list as $key => $value) {
            $data['news']['articles'][] = array(
                'title' => $value['product_name'],
                'description' => $value['product_desc'],
                'url' => C('WEB_SITE_URL') . U('Index/product', array('id'=>$value['product_id'])),
                'picurl' => C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true))
            );
            array_push($faing, $value['product_id']);
        }
        $success = $count = 0;
        foreach ($fans_list as $key => $value) {
            $data['touser'] = get_shop_info($value['user_id'], 'openid');
            $result = $this->WX->sendCustomMessage($data);
            $count++;
            if ($result) {
                $success++;
            }
        }

        $UserQunfa->add(array(
            'user_id' =>  $this->user_info['user_id'],
            'data' => implode(',', $faing),
            'time' => NOW_TIME
        ));

        $shop_data = array(
            'touser' => $this->user_info['openid'],
            'msgtype' => 'news',
            'news' => array(
                'articles' => array(
                    array(
                        'title' => '新品发布通知用户的报表',
                        'description' => '【拥抱豹哥】您好，您共有'. $count .'位中华聚宝朋友，已经通知'. $success .'位',
                        'url' => C('WEB_SITE_URL') . U('User/index'),
                        'picurl' => ''
                    )
                )
            )
        );
        $this->WX->sendCustomMessage($shop_data);
        $this->resultsuccess('拍品信息进入群发队列，稍后进行发送');
    }

    public function fenXiang($id = 0){
        if (empty($id)) {
            $this->resultError();
        }
        $Product = M('Product');
        $map = array(
            'user_id' =>  $this->user_info['user_id'],
            'id' => $id
        );
        $Product->where($map)->setField('is_share', 1);
        $this->resultsuccess();
    }

}
