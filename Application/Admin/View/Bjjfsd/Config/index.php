<style media="screen">
    .ms select {
        padding:0px 10px;height:24px;border:solid 1px #d2d2d2;margin-right:10px; background:#fafafa
    }
    .select {width:400px;}
    .text1{height:100px;}
	.head-a{margin: 10px 0px 20px 20px; border-bottom: 3px solid #343843; line-height: 32px;}
	.head-a a {
	    background-color: #08A3BB;
	    padding: 10px 20px;
	    color: #FFF;
	    font-weight: bold;
	}
	.head-a .act {background-color: #343843;}
</style>
<div class="head-a">
	<volist name="Think.config.CONFIG_GROUP_LIST" id="group">
		<a <eq name="id" value="$key">class="act"</eq> href="{:U('?id='.$key)}">{$group}配置</a>
	</volist>
</div>
<form action="{:U('save')}" method="post" class="form-horizontal">
<table width="900" border="0" cellspacing="0" cellpadding="0" class="table">
<volist name="list" id="config">
	<tr>
		<td class="td1" align="right">{$config['title']}：</td>
		<td class="ms">
		<switch name="config.type">
			<case value="0">
				<input type="text" class="inputt input" name="config[{$config['name']}]" value="{$config['value']}">
			</case>
			<case value="1">
				<input type="text" class="inputt input" name="config[{$config['name']}]" value="{$config['value']}">
			</case>
			<case value="2">
				<textarea class="text1" name="config[{$config['name']}]">{$config['value']}</textarea>
			</case>
			<case value="3">
				<textarea class="text1" name="config[{$config['name']}]">{$config['value']}</textarea>
			</case>
			<case value="4">
			<select class="select" name="config[{$config['name']}]">
				<volist name=":parse_config_attr($config['extra'])" id="vo">
					<option value="{$key}" <eq name="config.value" value="$key">selected</eq>>{$vo}</option>
				</volist>
			</select>
			</case>
		</switch>
	({$config['remark']})
	</volist>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="submit" class="tjanniu cr" value="提 交" />
			<input type="reset" class="czanniu cr" value="重 置" />
		</td>
	</tr>
</table>
</form>
