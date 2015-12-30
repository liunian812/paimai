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
            <input type="text" readonly="readonly" value="{:get_username()}" class="inputt input" />
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">密码：</td>
        <td class="ms">
            <input type="text" name="password" value="" class="inputt input" />
        </td>
    </tr>

    <tr>
        <td colspan="2" align="center">
             <input type="submit" class="tjanniu cr" value="提 交" /><input type="reset" class="czanniu cr" value="重 置" />
        </td>
    </tr>
</table>
</form>
