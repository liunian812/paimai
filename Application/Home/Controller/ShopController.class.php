<?php
namespace Home\Controller;
use Home\Common\IController;
class ShopController extends IController {
    protected $shop_info = array();
    protected $is_shop = 0;

    protected function _init(){
        parent::_init();
        $id = I('request.id', 0, 'intval');
        if (empty($id)) {
            $this->error('无效参数.....');
        }
        if ($this->user_info['user_id'] == $id) {
            $this->shop_info = $this->user_info;
            $this->is_shop = 1;

        } else {
            $this->shop_info = M('User')->find($id);
            if (empty($this->shop_info)) {
                $this->error('不存在该用户.....');
            } else {
                $this->shop_info['area'] = json_decode($this->shop_info['area'], true);
            }
            $this->is_shop = 0;
        }
        $this->assign('is_shop', $this->is_shop);
        $this->assign('shop_info', $this->shop_info);
    }

    public function index(){
        if (IS_AJAX) {
            $page_size = 10;
            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $map = array(
                'user_id' => $this->shop_info['user_id'],
                'end_time' => array('lt', NOW_TIME),
                'status' => array('gt', 1)
            );
            if ($this->is_shop) {
                $map['status'] = 2;
            }
            $Product = M('Product');
            $total_count = $Product->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $product_list = $Product->where($map)->order('update_time desc')->limit($start .','. $page_size)->select();
                foreach ($product_list as $key => $value) {
                    $product_list[$key]['product_img'] = array_shift(json_decode($value['product_images'], true));
                }
                $this->assign('product_type', C('PRODUCT_TYPE'));
                $this->assign('product_list', $product_list);
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
            $Product = M('Product');
            $map_sale = array(
                'user_id' => $this->shop_info['user_id'],
                'status' => 1,
                'begin_time' => array('lt', NOW_TIME),
                'end_time' => array('gt', NOW_TIME)
            );
            $sale_list = $Product->where($map_sale)->order('update_time desc')->select();
            foreach ($sale_list as $key => $value) {
                $sale_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
            }
            $this->assign('sale_list', $sale_list);

            $map_view = array(
                'user_id' => $this->shop_info['user_id'],
                'status' => 1,
                'begin_time' => array('gt', NOW_TIME),
                'end_time' => array('gt', NOW_TIME)
            );
            $view_list = $Product->where($map_view)->order('update_time desc')->select();
            foreach ($view_list as $key => $value) {
                $view_list[$key]['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($value['product_images'], true));
            }
            $this->assign('view_list', $view_list);
            $this->assign('product_type', C('PRODUCT_TYPE'));
            $this->display();
        }
    }

    public function user(){

        $this->display();
    }

}
