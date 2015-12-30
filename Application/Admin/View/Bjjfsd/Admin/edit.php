<style media="screen">
    .ms select {
        padding:0px 10px;height:24px;border:solid 1px #d2d2d2;margin-right:10px; background:#fafafa
    }
    .select {width:400px;}
    .text1{height:100px;}
</style>

<form action="" method="post">
<table width="900" border="0" cellspacing="0" cellpadding="0" class="table">
    <tr>
        <td class="td1" align="right">用户名：</td>
        <td class="ms">
            <input type="text" name="username" value="{$info['username']|default=''}" class="inputt input" />
            （用户名）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">密码：</td>
        <td class="ms">
            <input type="text" name="password" value="" class="inputt input" />
            （密码）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">管理组：</td>
        <td class="ms">
            <select class="select" name="group_id">
                <volist name="group_list" id="v">
                    <option value="{$key}" <eq name="info['group_id']" value="$key">selected="selected"</eq>>{$v['title']}</option>
                </volist>
            </select>
            （管理组）
        </td>
    </tr>
	<tr>
        <td class="td1" align="right">状态：</td>
        <td class="ms">
            <select class="select" name="status">
                    <option value="1" <eq name="info['status']" value="1">selected="selected"</eq>>正常</option>
                    <option value="0" <eq name="info['status']" value="0">selected="selected"</eq>>禁用</option>
            </select>
            （状态）
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
             <input type="hidden" name="user_id" value="{$info['user_id']|default=''}">
             <input type="submit" class="tjanniu cr" value="提 交" /><input type="reset" class="czanniu cr" value="重 置" />
        </td>
    </tr>
</table>
</form>
