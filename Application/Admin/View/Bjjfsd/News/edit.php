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
        <td class="td1" align="right">所属栏目：</td>
        <td class="ms">
            <select class="select" name="catid">
                <option value="">≡ 选择栏目 ≡</option>
        <volist name="cate_list" id="v">
            <if condition="$v['type'] != $type || $v['cat']">
                <option value="{$v['id']}" disabled="disabled" <eq name="v['id']" value="$info['catid']">selected="selected"</eq>>{$v['title']}</option>
            <else />
                <option value="{$v['id']}" <eq name="v['id']" value="$info['catid']">selected="selected"</eq>>{$v['title']}</option>
            </if>
        </volist>
            </select>
            （所属栏目）
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
            <input type="text" name="thumb" value="{$info['thumb']|default=''}" readonly="readonly" class="inputt input3">
            <input type="button" class="button1 cr" value="上 传">建议图片尺寸：{:get_news_px('width')}*{:get_news_px('height')}px;
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
        <td class="td1" align="right">内容：</td>
        <td class="ms">
            <script id="container" name="content" type="text/plain">{$info['content']|default=''|html_entity_decode}</script>
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

<!-- 百度编辑器 -->
<js href="__COMMON__js/jquery-2.0.2.js" />
<js href="__COMMON__ueditor/ueditor.config.js" />
<js href="__COMMON__ueditor/ueditor.all.min.js" />
<script>
    $(function(){
        var ue = UE.getEditor('container',{
            serverUrl :'{:U('Admin/Tool/ueditor')}',
            initialFrameWidth : 800,
            initialFrameHeight : 450
        });
    })
</script>
