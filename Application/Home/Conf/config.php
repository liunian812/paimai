<?php
return array(

    // 模版配置
    'TMPL_PARSE_STRING'  =>array(
         '__COMMON__' => __ROOT__ . '/Public/Common/',
         '__HOME__' => __ROOT__ . '/Public/' . MODULE_NAME . '/',
    ),

    //URL大小写区分
    'URL_CASE_INSENSITIVE'  =>  false,
    'URL_MODEL' => 2,
    // 普通模式           0
    // PATHINFO模式       1
    // REWRITE模式        2
    // 兼容模式           3

    'COOKIE_EXPIRE' => 3600,
    'COOKIE_DOMAIN' => '',
    'COOKIE_PREFIX' => 'jfsd_index_',

    // 开启路由
    'URL_ROUTER_ON'   => true,

    'URL_ROUTE_RULES'=>array(
        'lists' => array('Index/lists'),
        //'n/:id\d' => 'Index/lists/catid/:1'
    ),

    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR'     =>  'Public/error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  'Public/success', // 默认成功跳转对应的模板文件
    //'TMPL_EXCEPTION_FILE'   =>  'Public/exception',// 异常页面的模板文件

    // 自定义标签库
    'TAGLIB_BUILD_IN'   => 'Cx,Content',
    'TAGLIB_PRE_LOAD'   => 'Cx,Content',
);
