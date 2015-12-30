<?php
namespace Home\Controller;
use Home\Common\IController;
class CollectController extends IController {
    // 收藏的店铺
    public function index(){
        if (IS_AJAX) {
            $page_size = 10;
            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $map = array(
                'user_id' => $this->user_info['user_id'],
                'status' => 1
            );
            $UserCollect = M('UserCollect');
            $total_count = $UserCollect->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $collect_list = $UserCollect->where($map)->order('collect_id desc')->limit($start .','. $page_size)->select();
                $User = M('User');
                foreach ($collect_list as $key => $value) {
                    $collect_list[$key]['user_info'] = $User->find($value['shop']);
                }
                $this->assign('collect_list', $collect_list);
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

            $this->display();
        }
    }

    // 收藏的产品
    public function product(){
        if (IS_AJAX) {
            $page_size = 10;
            $page = I('page', 1, 'intval');
            $start = ($page - 1) * $page_size;
            $map = array(
                'user_id' => $this->user_info['user_id'],
                'status' => 1
            );
            $ProductCollect = M('ProductCollect');
            $total_count = $ProductCollect->where($map)->count();
            if ($total_count) {
                $total_page = ceil($total_count / $page_size);
                $collect_list = $ProductCollect->where($map)->order('collect_id desc')->limit($start .','. $page_size)->select();
                $User = M('User');
                $Product = M('Product');
                foreach ($collect_list as $key => $value) {
                    $collect_list[$key]['product_info'] = $Product->find($value['product']);
                    $collect_list[$key]['product_info']['product_img'] = array_shift(json_decode($collect_list[$key]['product_info']['product_images'],true));
                    $collect_list[$key]['user_info'] = $User->find($collect_list[$key]['product_info']['user_id']);
                }
                $this->assign('product_type', C('PRODUCT_TYPE'));
                $this->assign('collect_list', $collect_list);
                $content = $this->fetch('ajaxProduct');
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

    // 收藏
    public function enCollect(){
        $id = I('id', 0, 'intval');
        $type = I('type', 2041, 'intval');
        if ($id) {
            switch ($type) {
                case 2401: //店铺收藏
                    $UserCollect = M('UserCollect');
                    $map = array(
                        'user_id'=>$this->user_info['user_id'],
                        'shop'=>$id
                    );
                    $collect = $UserCollect->where($map)->find();
                    if ($collect) {
                        $data = array(
                            'status' => 1,
                            'update_time' => NOW_TIME
                        );
                        $result = $UserCollect->where($map)->save($data);
                    } else {
                        $data = array(
                            'user_id' => $this->user_info['user_id'],
                            'shop' => $id,
                            'status' => 1,
                            'create_time' => NOW_TIME,
                            'update_time' => NOW_TIME
                        );
                        $result = $UserCollect->add($data);
                    }
                    break;

                case 2400: //产品收藏
                    $ProductCollect = M('ProductCollect');
                    $map = array(
                        'user_id'=>$this->user_info['user_id'],
                        'product'=>$id
                    );
                    $collect = $ProductCollect->where($map)->find();
                    if ($collect) {
                        $data = array(
                            'status' => 1,
                            'update_time' => NOW_TIME
                        );
                        $result = $ProductCollect->where($map)->save($data);
                    } else {
                        $data = array(
                            'user_id' => $this->user_info['user_id'],
                            'product' => $id,
                            'status' => 1,
                            'create_time' => NOW_TIME,
                            'update_time' => NOW_TIME
                        );
                        $result = $ProductCollect->add($data);
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
        if ($result) {
            $this->resultSuccess();
        } else {
            $this->resultError();
        }
    }

    // 取消收藏
    public function unCollect(){
        $id = I('id', 0, 'intval');
        $type = I('type', 2041, 'intval');
        if ($id) {
            switch ($type) {
                case 2401: //店铺收藏
                    $UserCollect = M('UserCollect');
                    $map = array(
                        'user_id'=>$this->user_info['user_id'],
                        'shop'=>$id
                    );
                    $collect = $UserCollect->where($map)->find();
                    if ($collect) {
                        $data = array(
                            'status' => 0,
                            'update_time' => NOW_TIME
                        );
                        $result = $UserCollect->where($map)->save($data);
                    } else {
                        $result = 0;
                    }
                    break;

                case 2400: //产品收藏
                    $ProductCollect = M('ProductCollect');
                    $map = array(
                        'user_id'=>$this->user_info['user_id'],
                        'product'=>$id
                    );
                    $collect = $ProductCollect->where($map)->find();
                    if ($collect) {
                        $data = array(
                            'status' => 0,
                            'update_time' => NOW_TIME
                        );
                        $result = $ProductCollect->where($map)->save($data);
                    } else {
                        $result = 0;
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
        if ($result) {
            $this->resultSuccess();
        } else {
            $this->resultError();
        }
    }

    // 检查是否收藏
    public function isCollect(){
        $ids = array_unique((array)I('id',0));
        $type = I('type', 2041, 'intval');
        if ($ids) {
            $list = array();
            switch ($type) {
                case 2401: //店铺收藏
                    $UserCollect = M('UserCollect');
                    foreach ($ids as $id) {
                        $id = intval($id);
                        $map = array(
                            'user_id'=>$this->user_info['user_id'],
                            'shop'=>$id
                        );
                        $collect = $UserCollect->where($map)->find();
                        if ($collect['status']) {
                            $list[] = array('isCollected'=> true, id=>$id);
                        } else {
                            $list[] = array('isCollected'=> false, id=>$id);
                        }
                    }
                    break;

                case 2400: //产品收藏
                    $ProductCollect = M('ProductCollect');
                    foreach ($ids as $id) {
                        $id = intval($id);
                        $map = array(
                            'user_id'=>$this->user_info['user_id'],
                            'product'=>$id
                        );
                        $collect = $ProductCollect->where($map)->find();
                        if ($collect['status']) {
                            $list[] = array('isCollected'=> true, id=>$id);
                        } else {
                            $list[] = array('isCollected'=> false, id=>$id);
                        }
                    }
                    break;

                default:
                    # code...
                    break;
            }
            $this->resultSuccess('成功', array('list'=>$list));
        }
    }

}
