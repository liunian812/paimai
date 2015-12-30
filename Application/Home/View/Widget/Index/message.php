<div class="gywm" style="padding-left:120px;">
	<form id="form2" name="form2" method="post" action="{:U('Index/Index/message')}">
		<table width="476" border="0" cellpadding="0" cellspacing="1" align="left">
		  <tbody>
		<tr>
		    <td align="center"> 标题：</td>
		    <td align="left"><input name="title" type="text" id="title" class="input"></td>
		  </tr>
		  <tr>
		    <td align="center">邮箱：</td>
		    <td align="left"><input name="email" type="text" id="email" class="input"></td>
		  </tr>
		  <tr>
		    <td align="center">地址：</td>
		    <td align="left"><input name="extend[address]" type="text" id="address" class="input"></td>
		  </tr>
		  <tr>
		    <td align="center">电话：</td>
		    <td align="left"><input name="tel" type="text" id="tel" class="input"></td>
		 </tr>

		  <tr>
		    <td align="center" valign="top">内容：</td>
		    <td align="left" valign="top"><textarea style="margin-top:2px;" class="input" name="content" rows="6" cols="40" id="msg"></textarea></td>
		  </tr>
		  <tr>
		    <td align="right">&nbsp;</td>
		    <td align="center">
		      <input type="submit" value="提交" id="btnUp">
			</td>
		  </tr>
		</tbody></table>
	</form>
</div>
