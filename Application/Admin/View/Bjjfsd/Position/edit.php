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
        <td class="td1" align="right">类型：</td>
        <td class="ms">
            <select class="select" name="type">
                <volist name="Think.config.CATEGORY_TYPE" id="v">
                    <if condition="$key neq 'Pages'">
                        <option value="{$key}" <eq name="key" value="$info['type']">selected="selected"</eq>>{$v}</option>
                    </if>
                </volist>
            </select>
            （类型）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">标题：</td>
        <td class="ms">
            <input type="text" name="name" value="{$info['name']|default=''}" class="inputt input" />
            （标题）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">排序：</td>
        <td class="ms">
            <input type="text" name="sort" value="{$info['sort']|default=0}" class="inputt input" />
            （排序sort desc, id desc）
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
             <input type="hidden" name="id" value="{$info['id']|default=''}">
             <input type="submit" class="tjanniu cr" value="提 交" /><input type="reset" class="czanniu cr" value="重 置" />
        </td>
    </tr>
</table>
</form>
