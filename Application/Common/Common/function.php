<?php
/**
 *    共用函数库  2015年6月29日 16:15:03
 */

const CMS_VERSION    = '0.2_20150916';

// 微信支付日志
function  log_result($file,$word){
    $fp = fopen($file,"a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
 /**
  * 通用图片获取方法
  * @param  string $img 图片
  * @param  string $width 截图宽
  * @param  string $height 截图高
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
function image($img = '', $widht = 0, $height = 0) {
    if (!empty($img)) {
        $file =  'Uploads/' . $img;
        if (file_exists($file)) {
            return __ROOT__ .'/' . $file;
        } else {
            return __ROOT__ .'/Uploads/default.jpg';
        }
    } else {
        return __ROOT__ .'/Uploads/default.jpg';
    }
}

 /**
  * 系统用户MD5+sha1+key加密方法
  * @param  string $str 要加密的字符串
  * @param  string $key 加密key
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
 function password_md5($str, $key = 'bjjfsd123456'){
 	return '' === $str ? '' : md5(sha1($str) . $key);
 }

 /**
  * 字符串截取，支持中文和其他编码
  * @static
  * @access public
  * @param string $str 需要转换的字符串
  * @param string $start 开始位置
  * @param string $length 截取长度
  * @param string $charset 编码格式
  * @param string $suffix 截断显示字符
  * @return string
  */
 function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
     if(function_exists("mb_substr"))
         $slice = mb_substr($str, $start, $length, $charset);
     elseif(function_exists('iconv_substr')) {
         $slice = iconv_substr($str,$start,$length,$charset);
         if(false === $slice) {
             $slice = '';
         }
     }else{
         $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
         $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
         $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
         $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
         preg_match_all($re[$charset], $str, $match);
         $slice = join("",array_slice($match[0], $start, $length));
     }
     return $suffix ? $slice.'...' : $slice;
 }

 /**
  * 系统加密方法
  * @param string $data 要加密的字符串
  * @param string $key  加密密钥
  * @param int $expire  过期时间 单位 秒
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
 function encrypt($data, $key = '', $expire = 0) {
     $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
     $data = base64_encode($data);
     $x    = 0;
     $len  = strlen($data);
     $l    = strlen($key);
     $char = '';

     for ($i = 0; $i < $len; $i++) {
         if ($x == $l) $x = 0;
         $char .= substr($key, $x, 1);
         $x++;
     }

     $str = sprintf('%010d', $expire ? $expire + time():0);

     for ($i = 0; $i < $len; $i++) {
         $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
     }
     return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
 }

 /**
  * 系统解密方法
  * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
  * @param  string $key  加密密钥
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
 function decrypt($data, $key = ''){
     $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
     $data   = str_replace(array('-','_'),array('+','/'),$data);
     $mod4   = strlen($data) % 4;
     if ($mod4) {
        $data .= substr('====', $mod4);
     }
     $data   = base64_decode($data);
     $expire = substr($data,0,10);
     $data   = substr($data,10);

     if($expire > 0 && $expire < time()) {
         return '';
     }
     $x      = 0;
     $len    = strlen($data);
     $l      = strlen($key);
     $char   = $str = '';

     for ($i = 0; $i < $len; $i++) {
         if ($x == $l) $x = 0;
         $char .= substr($key, $x, 1);
         $x++;
     }

     for ($i = 0; $i < $len; $i++) {
         if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
             $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
         }else{
             $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
         }
     }
     return base64_decode($str);
 }

 /**
  * 检测输入的验证码是否正确
  * @param  string  $code 为用户输入的验证码字符串
  * @return bool
  * @author 拥抱 <572300808@qq.com>
  */
 function check_verify($code, $id = ''){
     $verify = new \Think\Verify();
     return $verify->check($code, $id);
 }
/**
 * 生成订单编号
 *
 */
function create_order_id(){
    return 'NS' . date('YmdHis') . mt_rand(1000, 9999);
}

/**
 * 获取店铺信息
 *
 */
function get_shop_info($user_id, $field = 'user_name'){
    $user_id = intval($user_id);
    if (empty($user_id)) {
        return '';
    }
    $user_info = M('User')->find($user_id);
    if (empty($user_info)) {
        return '';
    }
    if ($field) {
        return $user_info[$field];
    } else {
        return $user_info;
    }
}

// 微信昵称过滤
function remove_emoji($string) {
    return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $string);
}
/**
 * 获取产品信息
 *
 */
function get_product_info($product_id, $field = 'product_name'){
    $product_id = intval($product_id);
    if (empty($product_id)) {
        return '';
    }
    $product_info = M('Product')->find($product_id);
    if (empty($product_info)) {
        return '';
    }
    $product_info['url'] = U('Index/product', array('id'=>$product_id));
    $product_info['product_img'] = C('WEB_SITE_URL') . '/' . array_shift(json_decode($product_info['product_images'], true));
    if ($field) {
        return $product_info[$field];
    } else {
        return $product_info;
    }
}

/**
 * 获取包换服务
 * @param string $type 配置类型
 * @return string
 */
function get_baohuan_list($id=0){
    $list = C('BAOHUAN_LIST');
    return $id ? $list[$id] : '不包换';
}

/**
 * 获取包退服务
 * @param string $type 配置类型
 * @return string
 */
function get_baotui_list($id=0){
    $list = C('BAOTUI_LIST');
    return $id ? $list[$id] : '不包退';
}

/**
 * 获取快递公司
 * @param string $type 配置类型
 * @return string
 */
function get_kuaidi_gs($id=0){
    $list = C('KUAIDI_GS');
    return $id ? $list[$id] : '';
}

/**
 * 获取店铺等级
 * @param string $type 配置类型
 * @return string
 */
function get_shop_group($group=0){
    $list = C('SHOP_GROUP');
    return $group ? $list[$group] : '';
}

/**
 * 获取拍品的类型
 * @param string $type 配置类型
 * @return string
 */
function get_product_type($type=0){
    $list = C('PRODUCT_TYPE');
    return $type ? $list[$type] : '';
}

/**
 * 获取拍品的分类
 * @param string $type 分类
 * @return string
 */
function get_product_cate($cate=0){
    $list = C('PRODUCT_CATE');
    return $cate ? $list[$cate] : '';
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    $list = C('CONFIG_TYPE_LIST');
    return $type ? $list[$type] : '';
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    $list = C('CONFIG_GROUP_LIST');
    return $group ? $list[$group] : '';
}

/**
 * 获取数据类型的分组
 * @param string $group 类型
 * @return string
 */
function get_category_type($type=''){
    $list = C('CATEGORY_TYPE');
    return $type ? $list[$type] : '';
}

/**
 * 获取新闻图片尺寸
 * @param string $field 属性
 * @return string
 */
function get_news_px($field = ''){
    $list = C('NEWS_IMAGE_PX');
    return $field ? $list[$field] : '';
}

/**
 * 获取产品图片尺寸
 * @param string $field 属性
 * @return string
 */
function get_product_px($field = ''){
    $list = C('PRODUCT_IMAGE_PX');
    return $field ? $list[$field] : '';
}

/**
 * 获取广告类型
 * @param string $field 属性
 * @return string
 */
function get_banner_type($field = ''){
    $list = C('BANNER_TYPE');
    return $field ? $list[$field] : '';
}

//枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
   $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
   if(strpos($string,':')){
       $value  =   array();
       foreach ($array as $val) {
           list($k, $v) = explode(':', $val);
           $value[$k]   = $v;
       }
   }else{
       $value  =   $array;
   }
   return $value;
}

/**
* 删除目录和文件
* @author 拥抱 <572300808@qq.com>
*/
function delete_dir($path = '') {
   if(is_dir($path)) {
       $file_list= scandir($path);
       foreach ($file_list as $file) {
           if( $file!='.' && $file!='..') {
               delete_dir($path.'/'.$file);
           }
       }
       rmdir($path);
   } else {
       unlink($path);
   }
}


// 充值
function fund_inc($user_id, $price, $content = ''){
    $data = array(
        'user_id' => $user_id,
        'price' => $price,
        'action' => 1,
        'content' => $content,
        'create_time' => NOW_TIME,
        'update_time' => NOW_TIME,
        'status' => 1,
    );
    M('FundLog')->add($data);
    M('User')->where(array('user_id'=>$user_id))->setInc('fund', $price);
}

// 扣费
function fund_dec($user_id, $price, $content = ''){
    $data = array(
        'user_id' => $user_id,
        'price' => $price,
        'action' => 2,
        'content' => $content,
        'create_time' => NOW_TIME,
        'update_time' => NOW_TIME,
        'status' => 1,
    );
    M('FundLog')->add($data);
    M('User')->where(array('user_id'=>$user_id))->setDec('fund', $price);
}

// 增加积分
function add_integral($user_id = 0, $integral = 1)
{
    $User = M('User');
    $map = array('user_id'=>$user_id);
    $User->where($map)->setInc('integral', $integral);
    $integral = $User->where($map)->getField('integral');
    $integral_ext = C('INTEGRAL_EXT') ? C('INTEGRAL_EXT') : 1000;
    if ($integral > (6*$integral_ext)) {
        $User->where($map)->setField('shop_group', 7);
    } elseif ($integral > (5*$integral_ext)) {
        $User->where($map)->setField('shop_group', 6);
    } elseif ($integral > (4*$integral_ext)) {
        $User->where($map)->setField('shop_group', 5);
    } elseif ($integral > (3*$integral_ext)) {
        $User->where($map)->setField('shop_group', 4);
    } elseif ($integral > (2*$integral_ext)) {
        $User->where($map)->setField('shop_group', 3);
    } elseif ($integral > (1*$integral_ext)) {
        $User->where($map)->setField('shop_group', 2);
    } else {
        $User->where($map)->setField('shop_group', 1);
    }
}
