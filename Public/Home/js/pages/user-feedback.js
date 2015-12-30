/*!
* M.YS.COM v0.0.0
* Copyright 2015
* Author burning <iburning@live.cn>
*/
function log(a) {
   if (window._isDebug) {
       var a = $log.html();
       a += "<br />" + a + "<br />", $log.css("display", ""), $log.html(a)
   }
}
wx.ready(function() {
   log("wx ready")
}), wx.error(function(a) {
   log(JSON.stringify(a))
});
var $log = null;
$(function() {
   new YS.Form({form: $("#my-form"),beforeSubmit: function(a) {
           var b = [];
           return a.content || b.push("请输入您的问题或建议"), b.length ? (utils.showAlert({content: b.join("<br />")}), !1) : !0
       },successHandler: function(a, b) {
           1 === a.resultCode ? utils.showAlert({content: a.resultMessage,onButtonClick: function() {
                   utils.goUrl(a.goUrl || b)
               }}) : utils.showAlert({content: a.resultMessage})
       }});
   $(".btn-cancel").on("click", function(a) {
       utils.goUrl(_goodsListUrl)
   })
});
