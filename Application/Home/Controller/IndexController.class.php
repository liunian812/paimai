<?php
namespace Home\Controller;
use Home\Common\IController;
class IndexController extends IController {
    private $time = 3600;
    // 首页
    public function index(){
        $Product = M('Product');
        /* 秒杀 */
        $map = array(
            'product_type'=>3,
            'status' => 1,
            'position_1' => 1,
            'begin_time' => array('lt', NOW_TIME + $this->time),
            'end_time' => array('gt', NOW_TIME),
        );
        $miaosha_list = $Product->where($map)->order('sort asc')->select();
        foreach ($miaosha_list as $key => $value) {
            $miaosha_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
        }
        $this->assign('miaosha_list', $miaosha_list);
        /* 推荐拍品 */
        $map = array(
            'product_type'=> array('lt', 3),
            'status' => 1,
            'position_1' => 1,
            'begin_time' => array('lt', NOW_TIME + $this->time),
            'end_time' => array('gt', NOW_TIME),
        );
        $position_list = $Product->where($map)->order('sort asc')->select();
        foreach ($position_list as $key => $value) {
            $position_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
        }
        $this->assign('position_list', $position_list);
        /* VIP6店铺 */
        $map = array(
            'shop_group'=> 6,
            'status' => 1
        );
        $shop_list = M('User')->where($map)->order('sort asc')->select();
        $this->assign('shop_list', $shop_list);
        $this->display();
    }
    // 列表页
    public function lists($cate = 0, $type = 0){
        $this->assign('cate', $cate);
        $this->assign('type', $type);
        if (IS_AJAX) {
            $page_size = 10;
            $page = I('page', 1, 'intval');
            $index = I('index', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $map = array(
                'status' => 1,
                'begin_time' => array('lt', NOW_TIME + $this->time),
                'end_time' => array('gt', NOW_TIME),
            );
            if ($cate) {
                $map['product_cate'] = $cate;
            }
            if ($type) {
                $map['product_type'] = $type;
            }
            $Product = M('Product');
            $total_count = $Product->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $product_list = $Product->where($map)->order('update_time desc')->limit($start .','. $page_size)->select();
                foreach ($product_list as $key => $value) {
                    $product_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
                }
                $this->assign('product_type', C('PRODUCT_TYPE'));
                $this->assign('product_list', $product_list);

                $content = $this->fetch($index ? 'ajaxIndex' : 'ajaxLists');
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

    /* 拍品 */
    public function product($id){
        $Product = M('Product');
        $map = array('product_id' => $id);
        $product_info = $Product->where($map)->find();
        if (empty($product_info)) {
            exit;
        }
        $Product->where($map)->setInc('view');
        $product_info['user_info'] = M('User')->find($product_info['user_id']);

        // $product_info['begin_time'] = date('Y-m-d', $product_info['begin_time']);
        // $product_info['end_time'] = date('Y-m-d', $product_info['end_time']);
        // echo '<pre>'; print_r($product_info); exit;
        $product_info['product_images'] = json_decode($product_info['product_images'], true);
        $this->assign('product_type', C('PRODUCT_TYPE'));
        $this->assign('product_info', $product_info);
        $this->display();
    }

    public function faxian(){
        if (IS_AJAX) {
            $type = I('id', 0, 'intval');
            $Product = M('Product');
            $map = array(
                'status' => 1,
                'begin_time' => array('lt', NOW_TIME + $this->time),
                'end_time' => array('gt', NOW_TIME),
            );
            if ($type) {
                $map['product_type'] = $type;
            }
            $product_infos = $Product->where($map)->select();
            if ($product_infos) {
                $rand_key = array_rand($product_infos);
                $product_info = $product_infos[$rand_key];
                //print_r($product_info);
                $product_info['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($product_info['product_images'], true));
                $this->assign('product_info', $product_info);
            } else {
                $this->assign('product_info', array());
            }
            $this->display('ajaxFaxian');
        } else {
            $this->display();
        }
    }

    public function test(){
        $Product = M('Product');
        /* 秒杀 */
        $map = array(
            'product_type'=>3,
            'status' => 1,
            'position_1' => 1,
            'begin_time' => array('lt', NOW_TIME + $this->time),
            'end_time' => array('gt', NOW_TIME),
        );
        $miaosha_list = $Product->where($map)->order('sort asc')->select();
        foreach ($miaosha_list as $key => $value) {
            $miaosha_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
        }
        $this->assign('miaosha_list', $miaosha_list);
        /* 推荐拍品 */
        $map = array(
            'product_type'=> array('lt', 3),
            'status' => 1,
            'position_1' => 1,
            'begin_time' => array('lt', NOW_TIME + $this->time),
            'end_time' => array('gt', NOW_TIME),
        );
        $position_list = $Product->where($map)->order('sort asc')->select();
        foreach ($position_list as $key => $value) {
            $position_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
        }
        $this->assign('position_list', $position_list);
        /* VIP6店铺 */
        $map = array(
            'shop_group'=> 6,
            'status' => 1
        );
        $shop_list = M('User')->where($map)->order('sort asc')->select();
        $this->assign('shop_list', $shop_list);
        $this->display();
    }
}
