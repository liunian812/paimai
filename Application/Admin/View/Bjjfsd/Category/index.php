<form>
	<div class="hdtop">
    	<a href="{:U('add')}" class="tja">添 加</a>
        <div class="clear"></div>
    </div>
    <div class="hdbot">
    <style media="screen">
        .head910 td {
            background-color:#08a3bb;
            line-height: 33px;
            color: #fff;
            font-size: 14px;
        }
    </style>
    <table width="910" border="0" cellspacing="1" cellpadding="0" class="table1 tab">
        <tr class="head910">
            <td align="center">ID</td>
            <td align="center">名称</td>
            <td align="center">类型</td>
            <td align="center">导航显示</td>
            <td align="center">排序</td>
            <td align="center">操作</td>
        </tr>
        <volist name="list" id="v">
			<?php $v['setting'] = unserialize($v['setting']); ?>
            <tr>
                <td align="center">{$v['id']}</td>
                <td align="left">{$v['title']}</td>
                <td align="center">{$cateType[$v['type']]}</td>
                <td align="center">{$v['display']?'显示':'隐藏'}</td>
                <td align="center">{$v['sort']}</td>
                <td align="center">
                    <eq name="v['setting']['allow_child']" value="1"><a href="{:U('add?pid='. $v['id'])}" class="xga">添加子栏目</a>|</eq>
                    <a href="{:U('edit?id='. $v['id'])}" class="xga">修改</a>|
                    <a href="javascript:if(confirm('确认要执行该操作吗?')){location.href='{:U('del?id='. $v['id'])}'}" class="xga">删除</a>
                </td>
            </tr>
        </volist>
    </table>
</div>
</form>
