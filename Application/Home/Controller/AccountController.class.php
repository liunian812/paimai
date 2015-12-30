<?php
namespace Home\Controller;
use Home\Common\IController;
class AccountController extends IController {

    public function index(){

        $this->display();
    }

    // 个人认证
    public function preson(){
        if (IS_POST) {
            $extend = (array) I('extend');
            if (empty($extend[0])) {
                $this->resultError('真实姓名不能为空');
            }
            if (empty($extend[1])) {
                $this->resultError('身份证正面不能为空');
            }
            if (empty($extend[2])) {
                $this->resultError('身份证反面不能为空');
            }
            if (empty($extend[3])) {
                $this->resultError('开户银行不能为空');
            }
            if (empty($extend[4])) {
                $this->resultError('银行卡号不能为空');
            }
            $info = M('UserAuth')->where(array('user_id'=>$this->user_info['user_id'], 'type'=>1))->find();
            if ($info['status'] == 2) {
                $this->resultError('已经通过认证', '', U('index'));
            }
            if ($info) {
                $data = array(
                    'extend' => serialize($extend),
                    'update_time' => NOW_TIME,
                    'status' => 1   //0为拒绝，1为申请，2为通过
                );
                $result = M('UserAuth')->where(array('user_id'=>$this->user_info['user_id'], 'type'=>1))->save($data);
                if ($result) {
                    $this->resultSuccess('更新申请成功，等待管理员审核', '', U('index'));
                } else {
                    $this->resultError('失败');
                }
            } else {
                $data = array(
                    'type' => 1,
                    'user_id' => $this->user_info['user_id'],
                    'extend' => serialize($extend),
                    'create_time' => NOW_TIME,
                    'update_time' => NOW_TIME,
                    'status' => 1   //0为拒绝，1为申请，2为通过
                );
                $result = M('UserAuth')->add($data);
                if ($result) {
                    $this->resultSuccess('提交申请成功，等待管理员审核', '', U('index'));
                } else {
                    $this->resultError('失败');
                }
            }
        } else {
            $info = M('UserAuth')->where(array('user_id'=>$this->user_info['user_id'], 'type'=>1))->find();
            if ($info) {
                $this->assign('extend', unserialize($info['extend']));
            }
            $this->display();
        }
    }

    // 企业认证
    public function company(){
        if (IS_POST) {
            $extend = (array) I('extend');
            if (empty($extend[0])) {
                $this->resultError('真实姓名不能为空');
            }
            if (empty($extend[1])) {
                $this->resultError('身份证正面不能为空');
            }
            if (empty($extend[2])) {
                $this->resultError('身份证反面不能为空');
            }
            if (empty($extend[3])) {
                $this->resultError('营业执照不能为空');
            }
            if (empty($extend[4])) {
                $this->resultError('开户银行不能为空');
            }
            if (empty($extend[5])) {
                $this->resultError('银行卡号不能为空');
            }
            $info = M('UserAuth')->where(array('user_id'=>$this->user_info['user_id'], 'type'=>2))->find();
            if ($info['status'] == 2) {
                $this->resultError('已经通过认证', '', U('index'));
            }
            if ($info) {
                $data = array(
                    'extend' => serialize($extend),
                    'update_time' => NOW_TIME,
                    'status' => 1   //0为拒绝，1为申请，2为通过
                );
                $result = M('UserAuth')->where(array('user_id'=>$this->user_info['user_id'], 'type'=>1))->save($data);
                if ($result) {
                    $this->resultSuccess('更新申请成功，等待管理员审核', '', U('index'));
                } else {
                    $this->resultError('失败');
                }
            } else {
                $data = array(
                    'type' => 2,
                    'user_id' => $this->user_info['user_id'],
                    'extend' => serialize($extend),
                    'create_time' => NOW_TIME,
                    'update_time' => NOW_TIME,
                    'status' => 1   //0为拒绝，1为申请，2为通过
                );
                $result = M('UserAuth')->add($data);
                if ($result) {
                    $this->resultSuccess('提交申请成功，等待管理员审核', '', U('index'));
                } else {
                    $this->resultError('失败');
                }
            }
        } else {
            $info = M('UserAuth')->where(array('user_id'=>$this->user_info['user_id'], 'type'=>2))->find();
            if ($info) {
                $this->assign('extend', unserialize($info['extend']));
            }
            $this->display();
        }
    }



    // 提现
    public function tiXian(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'money' => I('money'),
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'status' => 1
            );
            if (empty($data['money'])) {
                $this->resultError('提现金额不能为空');
            }
            if ($data['money'] > $this->user_info['fund']) {
                $this->resultError('提现金额不能大于现有金额');
            }
            if ($data['money'] < 500) {
                $this->resultError('单次提现金额不能小于￥500');
            }

            $result = M('Tixian')->add($data);
            if ($result) {
                fund_dec($this->user_info['user_id'], $data['money'], '提现申请');
                $this->resultSuccess('成功，等待审核，预计72小时', '', U('index'));
            } else {
                $this->resultError();
            }
        } else {
            $this->display();
        }
    }

    // 认证图片上传（同步微信）
    public function sync(){
        $media_id = I('mediaId');
        $content = $this->WX->getMedia($media_id);
        $uploads_product_path = './Uploads/Auth/' . date('Y-m-d') . '/';
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
