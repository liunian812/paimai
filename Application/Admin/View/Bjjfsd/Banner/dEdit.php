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
        <td class="td1" align="right">广告位：</td>
        <td class="ms">
            <select class="select" name="bid">
                <option value="{$banner_info['id']}">{$banner_info['name']}</option>
            </select>
            （广告位）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">标题：</td>
        <td class="ms">
            <input type="text" name="title" value="{$info['title']|default=''}" class="inputt input" />
            （标题）
        </td>
    </tr>
    <tr>
        <td align="right">图片：</td>
        <td class="upload-row">
            <input type="text" name="image" value="{$info['image']|default=''}" readonly="readonly" class="inputt input3">
            <input type="button" class="button1 cr" value="上 传">建议图片尺寸：{$banner_info['width']}*{$banner_info['height']}px;
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">超链接：</td>
        <td class="ms">
            <input type="text" name="url" value="{$info['url']|default=''}" class="inputt input" />
            （超链接 ）
        </td>
    </tr>
    <tr>
        <td class="td1" align="right">描述：</td>
        <td class="ms">
            <textarea class="text1" name="description">{$info['description']|default=''}</textarea>
            （描述）
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
             <input type="hidden" name="id" value="{$info.id|default=''}">
             <input type="submit" class="tjanniu cr" value="提 交" /><input type="reset" class="czanniu cr" value="重 置" />
        </td>
    </tr>
</table>
</form>

<!-- 图片上传 -->
<script type="text/javascript">
    var Tool = {};
    $(function(){
        // 上传处理
        Tool.uploadSend = function(){
            $(".upload-row").each(function(i){
                $(this).find(".button1").click(function(){
                    window.open('{:U('Tool/uploadImage', '', '')}&id='+ i, '文件上传', 'height=100, width=400, top='+(screen.availHeight-100)/2+', left='+(screen.availWidth-400)/2+', toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no');
                })
            })
        }
        // 绑定
        Tool.uploadSend();
    })
</script>
