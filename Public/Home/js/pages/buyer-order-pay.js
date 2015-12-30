/*!
 * M.YS.COM v0.0.0
 * Copyright 2015
 * Author burning <iburning@live.cn>
 */
$(function(){var a=$("#my-form");a.find("button").on("click",function(b){var c=$(this).data("method");a.find('[name="tradeMethod"]').val(c)});new YS.Form({form:a,beforeSubmit:function(a){var b=[];return b.length?(utils.showAlert({content:b.join("<br />")}),!1):!0},successHandler:function(a,b){1===a.resultCode?utils.goUrl(a.goUrl||b):utils.showAlert({content:a.resultMessage})}})});