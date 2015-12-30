<?php
/**
 * seo方法未完成
 * @param   string
 * @return  string
 */
function seo() {

    return '';
}

/**
 * 获取图片
 * @param
 * @return  string
 */
function thumb($img = '', $width = 0, $height = 0) {
    if (empty($img)) {
        return __ROOT__ . '/default.jpg';
    }
    $Uploads = '/Uploads/';
    $file = '.' . $Uploads . $img;
    if (file_exists($file)) {
        if (empty($width)) {
            return __ROOT__ . substr($file, 1);
        } else {
            $pathinfo = pathinfo($file);
            $thumb_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . '-' . $height . '.' . $pathinfo['extension'];
            if (file_exists($thumb_file)) {
                return __ROOT__ . substr($thumb_file, 1);
            } else {
                $image = new \Think\Image();
                $image->open($file);
                if (empty($height)) {
                    $height = $image->height();
                }
                $image->thumb($width, $height,\Think\Image::IMAGE_THUMB_CENTER)->save($thumb_file);
                return __ROOT__ . substr($thumb_file, 1);
            }
        }
    }
    return __ROOT__ . '/default.jpg';
}

/**
 * 获取图片
 * @param
 * @return  string
 */
function video($video = '') {
    if (empty($video)) {
        return '';
    }
    $Uploads = '/Uploads/';
    $file = '.' . $Uploads . $video;
    if (file_exists($file)) {
        return __ROOT__ . substr($file, 1);
    }
    return '';
}

/**
 * 获取内容信息
 * @param   string       $content  内容
 * @return  string
 */
function get_content($content = ''){
    if ($content) {
        //return preg_replace('/src="(.*?)"/', 'src="'.__ROOT__.'$1"', html_entity_decode($content));
        return html_entity_decode($content);
    } else {
        return '';
    }
}

/**
 * 获取单页内容
 * @param  int  $catid
 * @return  string
 */
function get_page($catid = 0){
    if ($catid) {
        return M('Pages')->where(array('catid'=>$catid))->getField('content');
    } else {
        return '';
    }
}


function get_childs($catid){
	$cates = get_category($catid);
	if ($cates) {
		$str = '';
		foreach($cates as $key => $val){
			$str .= $key . ',';
			$str .= get_childs($key);
		}
		return $str;
	} else {
		return '';
	}
}

/**
 * 获取父级栏目
 * @param   int       $catid  栏目catid
 * @return  array
 */
function get_category($catid = 0, $num = 0) {
    $data = array();
    foreach ( D('Category')->getAll() as $key => $val) {
        if ($val['pid'] == $catid && $val['display']) {
            $data[$key] = $val;
            if ($num && count($data) >= $num) {
                break;
            }
        }
    }
    return $data;
}

/**
 * 获取广告位
 * @param   int         $id   广告位id
 * @param   int/string  $limit   数量
 * @return  array
 */
function get_banner($id = 0, $limit = 0){
    if (empty($id)) {
        return array();
    } else {
        $model = M('BannerData');
        $map = array();
        $map['bid'] = $id;
        if (empty($limit)) {
            $limit = '';
        }
        $lists = $model->cache(true, 60)->where($map)->order('sort desc, id desc')->limit($limit)->select();
        if ($lists) {
            return $lists;
        } else {
            return array();
        }
    }
}

/**
 * 获取广告位
 * @return  array
 */
function get_position($field = 'position_1', $type = 'News', $catid = 0, $limit = 0){
    $model = M('News');
    $map = array(
        'type' => $type,
        'status' => 1,
        $field => 1,
    );
    if ($catid) {
        $map['catid'] = $catid;
    }
    if (empty($limit)) {
        $limit = '';
    }
    $lists = $model->cache(true, 60)->where($map)->order('sort desc, id desc')->limit($limit)->select();
    if ($lists) {
        foreach ($lists as $k=> $v) {
            $lists[$k]['url'] = U('show', array('catid'=>$v['catid'],'id'=>$v['id']));
        }
        return $lists;
    } else {
        return array();
    }
}

/**
 * 获取列表
 * @return  array
 */
function get_lists($catid = 0, $type = 'News', $limit = 0){
    $model = M('News');
    $map = array(
        'type' => $type,
        'status' => 1
    );
    if ($catid) {
        $map['catid'] = $catid;
    }
    if (empty($limit)) {
        $limit = '';
    }
    $lists = $model->cache(true, 60)->where($map)->order('sort desc, id desc')->limit($limit)->select();
    if ($lists) {
        foreach ($lists as $k=> $v) {
            $lists[$k]['url'] = U('show', array('catid'=>$v['catid'],'id'=>$v['id']));
        }
        return $lists;
    } else {
        return array();
    }
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $ext 栏目间隔符
 */
function catpos($catid = 0, $ext = ' - ') {
    $categorys = D('Category')->getAll();
    $html = '';
    if ($catid == 0) {
        $html = '<a href="'. U('Index/index') .'">首页</a>' . $html;
        return $html;
    } else {
        $html = $ext . '<a href="' . $categorys[$catid]['url'] . '">' . $categorys[$catid]['title'] . '</a>' . $html;
        $html = catpos($categorys[$catid]['pid'], $ext) . $html;
    }
    return $html;
}

function addWeixinLog($data = '') {
    $log =  '====' . date( 'Y-m-d H:i:s') . '====' . PHP_EOL;
    $log .= $data . PHP_EOL . PHP_EOL;
    file_put_contents('weixin.log', $log, FILE_APPEND);
}

// 获取当前用户的Token
function get_token($token = NULL) {
    if ($token !== NULL) {
        session ( 'token', $token );
    } elseif (! empty ( $_REQUEST ['token'] )) {
        session ( 'token', $_REQUEST ['token'] );
    }
    $token = session ( 'token' );
    if (empty ( $token )) {
        $token = session('user_auth.token');
    }
    if (empty ( $token )) {
        return - 1;
    }

    return $token;
}

// 判断是否是在微信浏览器里
function isWeixinBrowser() {
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
        return false;
    }
    return true;
}

// php获取当前访问的完整url地址
function GetCurUrl() {
    $url = 'http://';
    if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
        $url = 'https://';
    }
    if ($_SERVER ['SERVER_PORT'] != '80') {
        $url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
    } else {
        $url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
    }
    // 兼容后面的参数组装
    // if (stripos ( $url, '?' ) === false) {
    //     $url .= '?t=' . time ();
    // }
    return $url;
}

//获取分享url的方法，解决controler在鉴权时二次回调jssdk获取分享url错误的问题
function get_shareurl(){
    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $findme   = 'https://open.weixin.qq.com/';
    $pos = strpos($url, $findme);
    // 使用 !== 操作符。使用 != 不能像我们期待的那样工作，
    // 因为 'a' 的位置是 0。语句 (0 != false) 的结果是 false。
    $share_url = '';
    if ($pos !== false) {             //url是微信的回调授权地址
        return '';
    } else {                           //url是本地的分享地址
        return $url;
    }
}


// 我收藏的店铺数
function my_collect_shop_num($user_id = 0) {
    $map = array(
        'user_id' => $user_id,
        'status' => 1
    );
    return M('UserCollect')->where($map)->count();
}

// 店铺收藏数
function collect_shop_num($shop = 0) {
    $map = array(
        'shop' => $shop,
        'status' => 1
    );
    return M('UserCollect')->where($map)->count();
}

// 我收藏的产品数
function my_collect_product_num($user_id = 0) {
    $map = array(
        'user_id' => $user_id,
        'status' => 1
    );
    return M('ProductCollect')->where($map)->count();
}

// 拍品收藏数
function collect_product_num($product = 0) {
    $map = array(
        'product' => $product,
        'status' => 1
    );
    return M('ProductCollect')->where($map)->count();
}
// 今后几天的数组列表
function time_list($days = 15) {
    $list = array();
    for ($i=0; $i < $days; $i++) {
        $list[] = date('Y-m-d', strtotime('+'. $i .'day'));
    }
    return $list;
}
// 获取最大出价
function max_bid_price($product_id){
    $bid_price = M('ProductBid')->where(array('product_id'=>$product_id))->max('bid_price');
    return $bid_price ? intval($bid_price) : 0;
}
// 判断出价是否领先
function check_bid_lead($product_id, $id = 0){
    $bid_id = M('ProductBid')->where(array('product_id'=>$product_id))->order('bid_price desc')->getField('bid_id');
    return ($id == $bid_id ) ? true : false;
}
// 判断用户出价是否领先
function check_user_bid($product_id, $user_id = 0){
    $user = M('ProductBid')->where(array('product_id'=>$product_id))->order('bid_price desc')->getField('user_id');
    return ($user == $user_id ) ? true : false;
}
// 时间格式化
function time_tran($the_time) {
    $now_time = date("Y-m-d H:i:s", time());
    $now_time = strtotime($now_time);
    $dur = $now_time - $the_time;
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200000000) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $the_time;
                    }
                }
            }
        }
    }
}

// 获取默认地址
function default_consign($user_id){
    if (empty($user_id)) {
        return array();
    }
    $map = array(
        'user_id' => $user_id,
        'is_default' => 1
    );
    $lists = M('userConsign')->where($map)->find();
    if ($lists) {
        $lists['area'] = json_decode($lists['area'], true);
        return $lists;
    } else {
        return array();
    }
}
