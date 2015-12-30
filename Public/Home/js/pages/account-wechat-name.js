/*!
 * M.YS.COM v0.0.0
 * Copyright 2015
 * Author burning <iburning@live.cn>
 */
$(function(){new YS.Form({form:$("#my-form"),beforeSubmit:function(a){var b=[];return a.wechatNumber||b.push("请输入微信号"),b.length?(utils.showAlert({content:b.join("<br />")}),!1):!0},successHandler:function(a,b){1===a.resultCode?utils.showAlert({content:a.resultMessage,onButtonClick:function(){utils.goUrl(a.goUrl||b)}}):utils.showAlert({content:a.resultMessage})}})});