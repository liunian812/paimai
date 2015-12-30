<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{:C('WEB_SITE_TITLE')}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="__ADMIN__css/style.css" rel="stylesheet" type="text/css" />
<link href="__ADMIN__css/tablestyle.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="__ADMIN__js/jquery-1.4.2.min.js"></script>
<script language="javascript" src="__ADMIN__js/js.js"></script>

</head>
<body>
<div class="container">
	<div class="head">
    	<div class="head1">{:C('WEB_SITE_TITLE')}</div>
        <div class="head2">
			<a href="{:U('Home/index/index')}" target="_blank"><img src="__ADMIN__images/ttb1.png" width="24" height="23" />网站首页</a>
        	<a href="{:U('Index/deleteCache')}" ><img src="__ADMIN__images/ttb2.png" width="24" height="23" />更新缓存</a>
            <a href="{:U('Config/index')}"><img src="__ADMIN__images/ttb2.png" width="24" height="23" />系统管理</a>
            <a href="{:U('Category/index')}"><img src="__ADMIN__images/ttb2.png" width="24" height="23" />内容管理</a>
            <a href="{:U('Index/help')}"><img src="__ADMIN__images/ttb3.png" width="24" height="23" />帮助中心</a>
            <a href="{:U('Public/logout')}"><img src="__ADMIN__images/ttb4.png" width="24" height="23" />安全退出</a>
        </div>
    </div>
    <div class="main">
    	<div class="mleft">
        	<ul class="ul">
			<volist name="__MENU__" id="val">
				<li><a href="{:U($val['name'])}">{$val['title']}</a>
				<notempty name="val['child']">
                	<ul class="ul1">
						<volist name="val['child']" id="v">

	                        <li <?php if($__ACT__ == strtolower($v['name'])){echo 'class="xz"';}?>><a href="{:U($v['name'])}">{$v['title']}</a>
						</volist>
                        <div class="lt"></div>
                    </ul>
				</notempty>
                </li>
			</volist>
            </ul>
        </div>
        <div class="mright">


        	<div class="mrtop">
            	<div class="breadCrumb">
                	您当前的位置： <a href="./admin.php">首页</a> &gt; {$meta_head} &gt; {$meta_title}
                </div>
                <div class="mrtr">
                	管理员：{:get_username()}
                </div>
                <div class="clear"></div>
            </div>
            <div class="mrbot">
            	<div class="mrbot1">
                	<div class="mrbt1">{$meta_title}</div>
                    <div class="mrnr1">
                    	{__CONTENT__}
                    </div>
                </div>
            </div>



            <div class="copyRight">北京金方时代科技有限公司&nbsp;&nbsp;&nbsp;版权所有 Inc All Rights Reserved&nbsp;&nbsp;&nbsp;联系电话：朝阳运营中心：010-51654311&nbsp;&nbsp;&nbsp;丰台运营中心：010-51654321&nbsp;&nbsp;&nbsp;海淀运营中心：010-51654300
			</div>
        </div>

        <div class="clear"></div>
    </div>
</div>
</body>
</html>
