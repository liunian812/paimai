<style media="screen">
    .glynr table {width: 100%; line-height: 35px; padding: 0 20px;}
    .glynr table td {padding: 5px; border-bottom: 1px solid #EEE;}
    .glynr table td img{vertical-align: middle;}
</style>
<div class="glynr">
    <table border="0" cellspacing="0" cellpadding="0">
        <if condition="$flags">
    		<tr>
    			<td colspan="2">
                    <a href="{:U('index?dir=' . base64_encode(dirname($dir_path)))}">返回上层目录</a>
                </td>
            </tr>
        </if>
        <volist name="list" id="val">
            <tr>
                <if condition="is_dir($val)">
                    <td>
                        <img src="__ADMIN__/ico/jia.ico" width="30" />
                        <a href="{:U('index?dir='. base64_encode($val))}"><b>{:basename($val)}</b></a>
                    </td>
                    <td width="10%"></td>
                <else />
                    <td><img src="__ADMIN__/ico/{:pathinfo($val, PATHINFO_EXTENSION)}.ico" width="20" /><a rel="">{:basename($val)}</a></td>
                    <td width="10%">
                        <a href="/{$val}" target="BigImg">查看</a>  |
                        <a href="{:U('delFile?dir='. base64_encode($val))}" onclick="return confirm('文件删除后将无法恢复，确认删除此文件吗？')">删除</a>
                    </td>
                </if>
            </tr>
        </volist>
    </table>
</div>
