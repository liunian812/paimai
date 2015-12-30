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
            <td align="center">名称</td>
            <td align="center">标题</td>
            <td align="center">分组</td>
            <td align="center">类型</td>
            <td align="center">操作</td>
        </tr>
        <volist name="list" id="v">
            <tr>
                <td align="center">{$v['name']}</td>
                <td align="center">{$v['title']}</td>
                <td align="center">{$v['group']|get_config_group}</td>
                <td align="center">{$v['type']|get_config_type}</td>
                <td align="center">
                    <a href="{:U('edit?id='. $v['id'])}" class="xga">修改</a>|
                    <a href="javascript:if(confirm('确认要执行该操作吗?')){location.href='{:U('del?id='. $v['id'])}'}" class="xga">删除</a>
                </td>
            </tr>
        </volist>
    </table>
    <div class="tableb">
		<div class="tablebnr page">
        	{$_page}
        </div>
    </div>
</div>
</form>
