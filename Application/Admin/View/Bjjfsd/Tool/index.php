<div style="min-height:500px;">
    <style media="screen">
        .table {line-height: 30px; font-size: 16px; margin: 20px; border:none;}
        .table th {font-weight: bold;  border-bottom: 1px solid #eee;}
        .table td {padding-left:30px; border-bottom: 1px solid #eee;}
    </style>
    <table class="table">
        <tr>
            <th>服务器操作系统</th>
            <td>{$Think.const.PHP_OS}</td>
        </tr>
        <tr>
            <th>ThinkPHP版本</th>
            <td>{$Think.VERSION}</td>
        </tr>
        <tr>
            <th>运行环境</th>
            <td>{$_SERVER['SERVER_SOFTWARE']}</td>
        </tr>
        <tr>
            <th>MYSQL版本</th>
            <php>
                $system_info_mysql = M()->query("select version() as v;");
            </php>
            <td>{$system_info_mysql.0.v}</td>
        </tr>
        <tr>
            <th>上传限制</th>
            <td>{:ini_get('upload_max_filesize')}</td>
        </tr>
    </table>
</div>
