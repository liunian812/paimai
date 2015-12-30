<?php
return array(
	// Layout配置
    'LAYOUT_ON' => true,
    'LAYOUT_NAME' => 'Layout/main',


    // 模版配置
    'TMPL_PARSE_STRING'  =>array(
         '__COMMON__' => __ROOT__ . '/Public/Common/',
         '__ADMIN__' => __ROOT__ . '/Public/' . MODULE_NAME . '/'
    ),


    'URL_MODEL' => 0,

    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR'     =>  'Public/error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  'Public/success', // 默认成功跳转对应的模板文件
    //'TMPL_EXCEPTION_FILE'   =>  'Public/exception',// 异常页面的模板文件

    //'IS_ROOT'   =>  array(1),   //array(1,2,3,4) 管理员id写入就会拥有超级管理员权限
);
