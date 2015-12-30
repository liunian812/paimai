<style media="screen">
    .ms select {
        padding:0px 10px;height:24px;border:solid 1px #d2d2d2;margin-right:10px; background:#fafafa
    }
    .select {width:400px;}
    .text1{height:100px;}
</style>

<table width="900" border="0" cellspacing="0" cellpadding="0" class="table">

    <tr>
        <td class="td1" align="right">标题：</td>
        <td class="ms">
            <input type="text" name="title" value="{$info['title']|default=''}" class="inputt input" />
            （标题）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">电话：</td>
        <td class="ms">
            <input type="text" name="tel" value="{$info['tel']|default=''}" class="inputt input" />
            （联系方式）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">邮箱：</td>
        <td class="ms">
            <input type="text" name="email" value="{$info['email']|default=''}" class="inputt input" />
            （邮箱）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">地址：</td>
        <td class="ms">
            <input type="text" name="extend[address]" value="{$info['extend']['address']|default=''}" class="inputt input" />
            （地址）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">内容：</td>
        <td class="ms">
            <textarea class="text1" name="content">{$info['content']|default=''}</textarea>
            （内容）
        </td>
    </tr>
</table>
