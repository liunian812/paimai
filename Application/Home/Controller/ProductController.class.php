<?php
namespace Home\Controller;
use Home\Common\IController;
class ProductController extends IController {

    public function index($action = 'sale'){
        if (IS_AJAX) {
            $page_size = 10;
            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $template = '';
            switch ($action) {
                case 'sale':
                    $map = array(
                        'user_id' => $this->user_info['user_id'],
                        'status' => 1,
                        'begin_time' => array('lt', NOW_TIME),
                        'end_time' => array('gt', NOW_TIME)
                    );
                    $template = 'ajaxSale';
                    break;
                case 'view':
                    $map = array(
                        'user_id' => $this->user_info['user_id'],
                        'status' => 1,
                        'begin_time' => array('gt', NOW_TIME),
                        'end_time' => array('gt', NOW_TIME)
                    );
                    $template = 'ajaxView';
                    break;
                case 'ware':
                    $map = array(
                        'user_id' => $this->user_info['user_id'],
                        'status' => 0
                    );
                    $template = 'ajaxWare';
                    break;
                default:
                    $map = array(
                        'user_id' => $this->user_info['user_id'],
                        'status' => 1,
                        'begin_time' => array('lt', NOW_TIME),
                        'end_time' => array('gt', NOW_TIME)
                    );
                    $template = 'ajaxSale';
                    break;
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
                $content = $this->fetch($template);
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
            $this->assign('action', $action);
            $this->display();
        }
    }

    public function add($type = 1, $id = 0){
        $Product = M('Product');
        if (IS_AJAX) {
            $file = (array)I('file');
            $data = array(
                'product_name' => I('productName'),
                'product_desc' => I('productDescription'),
                'product_images' => json_encode($file)
            );
            if (empty($data['product_name'])) {
                $this->resultError('拍品名称不能为空');
            }
            if (empty($data['product_desc'])) {
                $this->resultError('拍品描述不能为空');
            }
            if (!count($file)) {
                $this->resultError('拍品图片不能为空');
            }
            if (empty($id)) {
                $data['user_id'] = $this->user_info['user_id'];
                $data['product_type'] = $type;
                $data['create_time'] = NOW_TIME;
                $data['status'] = 0;
                $result = $Product->add($data);
                if ($result) {
                    add_integral($this->user_info['user_id']);
                    $this->resultSuccess('成功', '', U('editTo', array('id'=>$result)));
                } else {
                    $this->resultError();
                }
            } else {
                $product_info = $Product->find($id);
                if ($product_info['status'] == 1 && $product_info['begin_time'] <= NOW_TIME && $product_info['end_time'] >= NOW_TIME) {
                    $this->resultError('拍品中的产品不能修改');
                }
                $data['update_time'] = NOW_TIME;
                $map = array(
                    'user_id' => $this->user_info['user_id'],
                    'product_id' => $id
                );
                $result = $Product->where($map)->save($data);
                if ($result) {
                    $this->resultSuccess('成功', '', U('editTo', array('id'=>$id)));
                } else {
                    $this->resultError();
                }
            }
        } else {
            if ($id) {
                $product_info = $Product->find($id);
                $product_info['product_images'] = stripslashes($product_info['product_images']);
                $type = $product_info['product_type'];
                $this->assign('product_info', $product_info);
            }
            $this->assign('type', $type);
            $this->display();
        }
    }

    public function editTo($id = 0){
        if (IS_AJAX) {
            $data = array(
                'product_cate' => I('product_cate', 0, 'intval'),
                'product_type' => I('AuctionType', 0, 'intval'),
                'begin_time' => strtotime(I('beginDay') . I(beginHour)),
                'end_time' => strtotime(I('endDay') . I(endHour)),
                'start_price' => I('startPrice', 0, 'intval'),
                'range_price' => I('priceRange', 0, 'intval'),
                'reserve_price' => I('reservePrice', 0, 'intval'),
                'postage' => I('postage', 0, 'intval'),
                'baotui' => I('baotui', 1, 'intval'),
                'baohuan' => I('baohuan', 1, 'intval'),
                'update_time' => NOW_TIME,
                'status' => I('onsale', 0)
            );
            if ($data['begin_time'] >= $data['end_time']) {
                $this->resultError('结拍时间不能小于开拍时间...');
            }
            if (empty($data['product_cate'])) {
                $this->resultError('拍品分类不能为空');
            }
            switch ($data['product_type']) {
                case '1':
                    if (empty($data['range_price'])) {
                        $this->resultError('最低加价幅度不能为空');
                    }
                    break;
                case '2':
                    if (empty($data['range_price'])) {
                        $this->resultError('最低加价幅度不能为空');
                    }
                    if (empty($data['reserve_price'])) {
                        $this->resultError('保留价不能为空');
                    }
                    break;

                case '3':
                    if (empty($data['range_price'])) {
                        $this->resultError('秒杀价不能为空');
                    }
                    if (empty($data['reserve_price'])) {
                        $this->resultError('秒杀数量不能为空');
                    }
                    break;

                default:
                    # code...
                    break;
            }

            $map = array(
                'user_id' => $this->user_info['user_id'],
                'product_id' => I('productId', 0, 'intval')
            );
            $result = M('Product')->where($map)->save($data);
            if ($result) {
                if ($data['status']) {
                    if (!$this->user_info['is_subscribe']) {
                        $this->resultError('请先关注公众平台【'. C('WEB_SITE_TITLE') .'】', '', 'http://mp.weixin.qq.com/s?__biz=MzI2OTA4MzI4NA==&mid=401275684&idx=4&sn=215571f800f39c82095f7dca83728a0e#rd');
                    }
                    if (!$this->user_info['mobile']) {
                        $this->resultError('请先完善个人资料手机号参数必填', '', U('Setting/index'));
                    }
                }
                $this->resultSuccess('成功', '', U('Product/index', array('action'=>'ware')));
            } else {
                $this->resultError();
            }
        } else {
            $product_info = M('Product')->find($id);
            $this->assign('product_info', $product_info);
            $this->display();
        }
    }

    // 上下架处理
    public function sale($productId, $status){
        $Product = M('Product');
        if ($status) {
            if (!$this->user_info['is_subscribe']) {
                $this->resultError('请先关注公众平台【'. C('WEB_SITE_TITLE') .'】', '', 'http://mp.weixin.qq.com/s?__biz=MzI2OTA4MzI4NA==&mid=401275684&idx=4&sn=215571f800f39c82095f7dca83728a0e#rd');
            }
            if (!$this->user_info['mobile']) {
                $this->resultError('请先完善个人资料手机号参数必填', '', U('Setting/index'));
            }
            $product_info = $Product->find($productId);
            if ($product_info['begin_time'] >= $product_info['end_time'] || $product_info['end_time'] <= NOW_TIME) {
                $this->resultError('正确修改开拍时间和结拍时间', '', U('Product/editTo', array('id'=>$productId)));
            }
        } else {
            if (max_bid_price($productId)) {
                $this->resultError('已有人出价无法下架');
            }
        }
        $data = array(
            'status' => $status,
            'update_time' => NOW_TIME
        );
        $map = array(
            'user_id' => $this->user_info['user_id'],
            'product_id' => $productId
        );
        $result = $Product->where($map)->save($data);
        if ($result) {
            $this->resultSuccess();
        } else {
            $this->resultError();
        }
    }

    // 拍品删除
    public function del($productId){
        if (max_bid_price($productId)) {
            $this->resultError('已有人出价无法删除');
        }
        $Product = M('Product');
        $map = array(
            'user_id' => $this->user_info['user_id'],
            'product_id' => $productId
        );
        $result = $Product->where($map)->delete();
        if ($result) {
            $this->resultSuccess();
        } else {
            $this->resultError();
        }
    }

    // 拍品图片上传（同步微信）
    public function sync(){
        $media_id = I('mediaId');
        $content = $this->WX->getMedia($media_id);
        $uploads_product_path = './Uploads/Product/' . date('Y-m-d') . '/';
        if (!file_exists($uploads_product_path)) {
            @mkdir($uploads_product_path, 0777, true);
        }
        $filename = $uploads_product_path . $media_id . '.jpg';
        if ($content) {
            file_put_contents($filename, $content);
            $value = array(
                'info' => array(
                    'url' => $filename
                )
            );
            $this->resultSuccess('上传成功', $value);
        } else {
            $this->resultError('上传失败');
        }

    }
}
