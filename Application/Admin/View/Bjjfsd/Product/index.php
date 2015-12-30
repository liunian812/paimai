<div class="hdtop">
	<a href="{:U('add')}" class="tja">添 加</a>
	<div class="hdtright">
		<form action="" method="get">
		<span>标 题：</span><input type="text" name="title" value="{$search['title']|default=''}" class="inputt input1" />
		<span>所属分类：</span>
		<select name="catid" class="easyui-combobox" style="width:130px;height:25px">
			<option value="">全部</option>
			<volist name="cate_list" id="v">
			<if condition="$v['type'] != $type || $v['cat']">
				<option value="{$v['id']}" disabled="disabled" <eq name="v['id']" value="$search['catid']">selected="selected"</eq>>{$v['title']}</option>
			<else />
				<option value="{$v['id']}" <eq name="v['id']" value="$search['catid']">selected="selected"</eq>>{$v['title']}</option>
			</if>
			</volist>
		</select>
		<input type="hidden" name="m" value="Admin" />
		<input type="hidden" name="c" value="News" />
		<input type="hidden" name="a" value="index" />
		<input type="submit" value="查 询" class="button" />
		</form>
	</div>
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
		<td align="center"><input type="checkbox" class="allcheck">ID</td>
		<td align="center">标题</td>
		<td align="center">所属栏目</td>
		<td align="center">排序</td>
		<td align="center">发布时间</td>
		<td align="center">操作</td>
	</tr>
	<volist name="list" id="v">
		<tr>
			<td align="center"><input type="checkbox" name="id" value="{$v['id']}">{$v['id']}</td>
			<td align="center">{$v['title']}</td>
			<td align="center">{$category[$v['catid']]['title']}</td>
			<td align="center">{$v['sort']}</td>
			<td align="center">{:date('Y-m-d H:i:s', $v['create_time'])}</td>
			<td align="center">
				<a href="{:U('edit?id='. $v['id'])}" class="xga">修改</a>|
				<a href="javascript:if(confirm('确认要执行该操作吗?')){location.href='{:U('del?id='. $v['id'])}'}" class="xga">删除</a>
			</td>
		</tr>
	</volist>
</table>
<style media="screen">
	.position {
		margin-top: 12px;
		height: 25px;
		background-color: #FF9A1A;
		border: 1px solid #E5EB1B;
		color: #FFF;
	}
</style>
<div class="tableb">
	<input type="checkbox" class="allcheck">
	<input type="button" id="del" value="删除" class="scanniu cr">
	<select class="position" name="">
		<option value="0">推荐位</option>
		<volist name="position" id="val">
			<option value="{$val['id']}">{$val['name']}</option>
		</volist>
	</select>
	<div class="tablebnr page">
		{$_page}
	</div>
</div>
</div>
<script type="text/javascript">
var Tool = {};
$(function(){
	$("#del").click(function(){
		var xx = confirm('是否确认操作！');
		if(xx){
			var ids = [];
			$("[name=id]:checkbox:checked").each(function(){
				ids.push($(this).val());
			});
			if (ids.length == 0) {
				alert('请选择操作对象');
				return false;
			}
			$.ajax({
				url: '{:U('del')}',
				type: 'post',
				data: {id: ids},
				dataType: 'json',
				success: function(data){
					if (data.status) {
						alert(data.info);
						location.reload();
					} else {
						alert(data.info);
					}
				},
				error: function(){
					alert('网络异常...');
				}
			});
		}
	});
	$(".position").change(function(){
		var ids = [];
		$("[name=id]:checkbox:checked").each(function(){
			ids.push($(this).val());
		});
		if (ids.length == 0) {
			alert('请选择操作对象');
			return false;
		}
		$.ajax({
			url: '{:U('Position/dAdd')}',
			type: 'post',
			data: {pid: $(this).val(), id: ids},
			dataType: 'json',
			success: function(data){
				if (data.status) {
					alert(data.info);
					location.reload();
				} else {
					alert(data.info);
				}
			},
			error: function(){
				alert('网络异常...');
			}
		});
	});
})
</script>
