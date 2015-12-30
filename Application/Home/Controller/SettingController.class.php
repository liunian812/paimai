<?php
namespace Home\Controller;
use Home\Common\IController;
class SettingController extends IController {

    public function index(){
        $map = array(
            'user_id' => $this->user_info['user_id'],
            'is_default' => 1,
        );
        $user_consign = M('UserConsign')->where($map)->find();
        $user_consign['area'] = json_decode($user_consign['area'], true);
        $this->assign('user_consign', $user_consign);
        $this->display();
    }
    // 收货地址列表
    public function addressList(){
        if (IS_AJAX) {
            $data = array(
                'goUrl' => '',
                'resultCode' => 1,
                'resultMessage' => '',
                'value' => array(
                    'htmlString' => '',
                    'info' => '',
                    'isNextPage' => 'false',
                    'list' => '',
                    'page' => 10,
                    'pageSize' => 10,
                    'totalCount' => 5,
                    'totalPage' => 1
                )
            );
            $this->ajaxReturn($data);
        } else {
            $map = array();
            $map['user_id'] = $this->user_info['user_id'];
            $consign_list = M('UserConsign')->where($map)->order('is_default desc, address_id desc')->select();
            $this->assign('consign_list', $consign_list);
            $order_id = I('order_id');
            if ($order_id) {
                $this->assign('order_id', $order_id);
            } else {
                $this->assign('order_id', 0);
            }
            $this->display();
        }
    }
    // 添加收货地址
    public function addAddress(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'consign_name' => I('consignName'),
                'mobile' => I('mobile'),
                'area' => json_encode(array(
                    'province' => I('province'),
                    'city' => I('city'),
                    'area' => I('area')
                )),
                'address' => I('address'),
                'zipcode' => I('zipcode'),
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'status' => 1
            );
            if (M('UserConsign')->where(array('user_id'=>$this->user_info['user_id']))->count()) {
                $data['is_default'] = 0;
            } else {
                $data['is_default'] = 1;
            }
            $result = M('UserConsign')->add($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('addressList')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        } else {
            $this->display();
        }
    }

    // 修改收货地址
    public function editAddress(){
        if (IS_POST) {
            $address_id = I('userAddressId');
            $data = array(
                'consign_name' => I('consignName'),
                'mobile' => I('mobile'),
                'area' => json_encode(array(
                    'province' => I('province'),
                    'city' => I('city'),
                    'area' => I('area')
                )),
                'address' => I('address'),
                'zipcode' => I('zipcode'),
                'update_time' => NOW_TIME
            );
            $map = array(
                'address_id' => $address_id,
                'user_id' => $this->user_info['user_id']
            );
            $result = M('UserConsign')->where($map)->save($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('addressList')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        } else {
            $address_id = I('address_id');
            $address_info = M('UserConsign')->find($address_id);
            if ($address_info) {
                $address_info['area'] = json_decode($address_info['area'], true);
                $this->assign('address_info', $address_info);
            } else {
                $this->assign('address_info', array());
            }
            $this->display('addAddress');
        }
    }
    // 设置默认
    public function setDefault(){
        if (IS_AJAX) {
            $address_id = I('addressId');
            $data = array(
                'is_default' => 1,
                'update_time' => NOW_TIME
            );
            $map = array(
                'address_id' => $address_id,
                'user_id' => $this->user_info['user_id']
            );
            M('UserConsign')->where(array('user_id' => $this->user_info['user_id']))->setField('is_default', 0);
            $result = M('UserConsign')->where($map)->save($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('addressList')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        }

    }
    // 删除收货地址
    public function delAddress(){
        if (IS_AJAX) {
            $address_id = I('addressId');
            $map = array(
                'address_id' => $address_id,
                'user_id' => $this->user_info['user_id']
            );
            $result = M('UserConsign')->where($map)->delete();
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('addressList')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        }
    }

    // 修改微信
    public function editWx(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'wechat_number' => I('wechatNumber'),
                'update_time' => NOW_TIME
            );
            $result = M('User')->save($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('Setting/index')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        } else {
            $this->display();
        }
    }
    // 修改主营项目
    public function editMainBues(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'main_business' => I('mainBusiness'),
                'update_time' => NOW_TIME
            );
            $result = M('User')->save($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('Setting/index')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        } else {
            $this->display();
        }
    }
    // 修改签名
    public function editSign(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'signature' => I('signature'),
                'update_time' => NOW_TIME
            );
            $result = M('User')->save($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('Setting/index')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        } else {
            $this->display();
        }
    }
    // 修改地区
    public function editArea(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'area' => json_encode(array(
                    'province' => I('province'),
                    'city' => I('city'),
                    'area' => I('area')
                )),
                'update_time' => NOW_TIME
            );
            $result = M('User')->save($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('Setting/index')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        } else {
            $this->display();
        }
    }

    // 修改手机号
    public function editMobile(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'mobile' => I('mobile'),
                'update_time' => NOW_TIME
            );
            $result = M('User')->save($data);
            if ($result) {
                $result = array(
                    'resultMessage' => '修改成功',
                    'value' => '1',
                    'resultCode' => 1,
                    'goUrl' => U('Setting/index')
                );
                $this->ajaxReturn($result);
            } else {
                $result = array(
                    'resultMessage' => '修改失败',
                    'value' => '0',
                    'resultCode' => -1,
                    'goUrl' => ''
                );
                $this->ajaxReturn($result);
            }
        } else {
            $this->display();
        }
    }


}
