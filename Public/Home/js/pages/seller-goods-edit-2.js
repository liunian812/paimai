/*!
 * M.YS.COM v0.0.0
 * Copyright 2015
 * Author burning <iburning@live.cn>
 */
$(function(){var a=!1;window._backUrl&&($(window).on("popstate",function(){a&&(location.href=window._backUrl)}),window.setTimeout(function(){"#flag"!==location.hash&&(window.history.pushState({},"",location.href+"#flag"),a=!0)},500));var b=$("#my-form");b.find(".btn-save").on("click",function(a){b.find('input[name="onsale"]').val(0),b.submit()});new YS.Form({form:b,beforeSubmit:function(a){var b=[];return b.length?(utils.showAlert({content:b.join("<br />")}),!1):!0},successHandler:function(a,b){1===a.resultCode?utils.goUrl(a.goUrl||b):utils.showAlert({content:a.resultMessage})}})});