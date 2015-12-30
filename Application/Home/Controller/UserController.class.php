<?php
namespace Home\Controller;
use Home\Common\IController;
class UserController extends IController {

    public function index(){

        $this->display();
    }

    // 同步微信信息
    public function sync(){
        $wx_info = $this->WX->getUserInfo($this->user_info['openid']);
        $data = array(
            'user_id' => $this->user_info['user_id'],
            'user_name' => remove_emoji($wx_info['nickname']),
            'user_avatar' => $wx_info['headimgurl'],
            'update_time' => NOW_TIME
        );
        M('User')->save($data);
        $this->redirect('User/index');
    }

    // 强红包
    public function hongBao(){
        if (!C('HONG_BAO_ALLOW')) {
            $this->resultError('未开启拆红包功能');
        }
        if (!$this->user_info['is_subscribe']) {
            $this->resultError('请先关注公众平台【'. C('WEB_SITE_TITLE') .'】', '', 'http://mp.weixin.qq.com/s?__biz=MzI2OTA4MzI4NA==&mid=401275684&idx=4&sn=215571f800f39c82095f7dca83728a0e#rd');
        }
        if (!$this->user_info['mobile']) {
            $this->resultError('请先完善个人资料手机号参数必填', '', U('Setting/index'));
        }
		// $this->resultError('end', '', U('Setting/index'));
        $shop_id    = I('shop_id', 0, 'intval');
        if (!$shop_id) {
            $this->resultError('无效店铺参数');
        }
        $Hongbao = M('Hongbao');
        $map = array(
            'shop_id'       => $shop_id,
            'user_id'       => $this->user_info['user_id'],
            'create_time'   => array('gt', strtotime(date('Ymd'))),
            'status'        => 1
        );
        $count = $Hongbao->where($map)->count();
        if ($count) {
            $this->resultError('每天每个店铺只能领取一次');
        }
        $moneys = explode('-', C('HONG_BAO_MONEY'));
        if (count($moneys) == 1) {
            $money = $moneys[0];
        } elseif (count($moneys) == 2) {
            $money = mt_rand($moneys[0], $moneys[1]);
        }
        $data = array(
            'user_id'       => $this->user_info['user_id'],
            'shop_id'       => $shop_id,
            'money'         => $money,
            'create_time'   => NOW_TIME,
            'update_time'   => NOW_TIME,
            'status'        => 1
        );
        $result = $Hongbao->add($data);
        if ($result) {
            fund_inc($this->user_info['user_id'], $money, '拆红包');
            // 给卖家发
            // $data = array(
                // 'first' => '你好，【'. $this->user_info['user_name'] .'】获得￥'. $money,
                // 'product_id' => $result,
                // 'product_name' => '抢红包活动',
                // 'remark' => '抢红包时间：' . date('Y-m-d H:i:s')
            // );
            // $this->sendBid($this->user_info['openid'], U('User/index'), $data);
			$shop_data = array(
				'touser' => $this->user_info['openid'],
				'msgtype' => 'news',
				'news' => array(
					'articles' => array(
						array(
							'title' => '抢购红包成功提示',
							'description' => '你好，【'. $this->user_info['user_name'] .'】获得￥'. $money . PHP_EOL . '抢红包时间：' . date('Y-m-d H:i:s'),
							'url' => C('WEB_SITE_URL') . U('User/index'),
							'picurl' => ''
						)
					)
				)
			);
			$this->WX->sendCustomMessage($shop_data);
            $this->resultSuccess('抢红包获得￥'. $money);
        } else {
            $this->resultError();
        }
    }

    // 留言
    public function message(){
        if (IS_POST) {
            $data = array(
                'user_id' => $this->user_info['user_id'],
                'email' => I('email'),
                'content' => I('content'),
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME
            );
            if (empty($data['content'])) {
                $this->resultError('内容不能为空');
            }
            $result = M('Message')->add($data);
            if ($result) {
                $this->resultSuccess('成功', '', U('index'));
            } else {
                $this->resultError();
            }
        } else {
            $this->display();
        }
    }


}
