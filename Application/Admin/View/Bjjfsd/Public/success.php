<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>信息提示</title>
<style media="screen">
    *{ margin:0px; padding:0px;}
    body{ font-size:12px; font-family:"微软雅黑"; background:#fff;}
    body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, p, b, em, span, i, pre, form, fieldset, label, input, textarea, blockquote{
        margin: 0;
        padding: 0;
        list-style:none;
    }
    img{ display:block; border:0px;}
    a{ text-decoration:none; color:#000;}
    a:hover{ text-decoration:none;}
    li{ list-style:none;}
    .f{ font-family:"宋体";}
    .clear{ clear:both;}

    /*成功*/
    .success{ width:398px; height:115px; margin:0 auto; font-size:16px; color:#000000; background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGwAAABzCAYAAAB95ueDAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjU0MTlCNkQ3Mzc0ODExRTVCNkEyRjc4MzgzMTE5MzUwIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjU0MTlCNkQ4Mzc0ODExRTVCNkEyRjc4MzgzMTE5MzUwIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NTQxOUI2RDUzNzQ4MTFFNUI2QTJGNzgzODMxMTkzNTAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NTQxOUI2RDYzNzQ4MTFFNUI2QTJGNzgzODMxMTkzNTAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7dA0dSAAAQZElEQVR42uxdCXhVxRU+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") left center no-repeat; padding-left:140px; line-height:32px; position: absolute; top: 50%; margin-top: -58px; left: 50%; margin-left: -199px;}
    .success span{ display:block; font-size:30px; color:#000000; line-height:48px; padding-top:6px;}
    .success a{ display:inline-block; width:75px; height:19px; line-height:19px; text-align:center; background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEsAAAATCAYAAADYk/BwAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjVBOEM3NTM5Mzc0NTExRTVBQUU3Qjg4MDIyMDlBNjRCIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjVBOEM3NTNBMzc0NTExRTVBQUU3Qjg4MDIyMDlBNjRCIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NUE4Qzc1MzczNzQ1MTFFNUFBRTdCODgwMjIwOUE2NEIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NUE4Qzc1MzgzNzQ1MTFFNUFBRTdCODgwMjIwOUE2NEIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4W0oDlAAAAYElEQVR42uzQQQ2AMBBE0aUnLCAJHWCrN2wghxQZtAIgy/39ZJI5v2k+zjUiat8Seqv1bQVUquFTC6g8WGGQDxYsWLBgwRIsWLBgwYIlWD+xGoZU98Dax2Hx2dW3PQIMAIJ7Cdmfc1JmAAAAAElFTkSuQmCC") center center no-repeat; font-size:12px; color:#ffffff; margin-left:19px;}

    /*失败*/
    .error{ width:398px; height:115px; margin:0 auto; font-size:16px; color:#000000; background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG0AAABlCAYAAABHq207AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjNBREQ3NTM1Mzc0NTExRTVCMDc2RTU1NDUxREE3MDFCIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjNBREQ3NTM2Mzc0NTExRTVCMDc2RTU1NDUxREE3MDFCIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6M0FERDc1MzMzNzQ1MTFFNUIwNzZFNTU0NTFEQTcwMUIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6M0FERDc1MzQzNzQ1MTFFNUIwNzZFNTU0NTFEQTcwMUIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6jEUr7AAAR1klEQVR42txdC3CU1RU+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") left center no-repeat; padding-left:140px; line-height:32px; position: absolute; top: 50%; margin-top: -58px; left: 50%; margin-left: -199px;}
    .error span{ display:block; font-size:30px; color:#000000; line-height:48px; padding-top:6px;}
    .error a{ display:inline-block; width:75px; height:19px; line-height:19px; text-align:center; background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEsAAAATCAYAAADYk/BwAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjVBOEM3NTM5Mzc0NTExRTVBQUU3Qjg4MDIyMDlBNjRCIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjVBOEM3NTNBMzc0NTExRTVBQUU3Qjg4MDIyMDlBNjRCIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NUE4Qzc1MzczNzQ1MTFFNUFBRTdCODgwMjIwOUE2NEIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NUE4Qzc1MzgzNzQ1MTFFNUFBRTdCODgwMjIwOUE2NEIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4W0oDlAAAAYElEQVR42uzQQQ2AMBBE0aUnLCAJHWCrN2wghxQZtAIgy/39ZJI5v2k+zjUiat8Seqv1bQVUquFTC6g8WGGQDxYsWLBgwRIsWLBgwYIlWD+xGoZU98Dax2Hx2dW3PQIMAIJ7Cdmfc1JmAAAAAElFTkSuQmCC") center center no-repeat; font-size:12px; color:#ffffff; margin-left:19px;}
</style>
</head>
<body>
<div class="success">
    <span><?php echo $message;?></span>
    页面自动  跳转  等待时间：<b id="wait"><?php echo($waitSecond); ?></b><a id="href" href="<?php echo($jumpUrl); ?>">手动跳转</a>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait');
var href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
