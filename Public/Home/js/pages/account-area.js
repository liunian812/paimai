/*!
* M.YS.COM v0.0.0
* Copyright 2015
* Author burning <iburning@live.cn>
*/
$(function() {
   var a = null, b = null, c = null;
   a = new LocalSelector({el: $("#province"),defaultItem: '<option value="" data-code="">省/直辖市</option>',data: provinceData,value: _province,onChange: function(a, d) {
           var e = getCitiesByProvinceCode(d);
           b.setValue(""), b.setData(e), c.setValue(""), c.setData(null)
       }}), b = new LocalSelector({el: $("#city"),defaultItem: '<option value="" data-code="">市</option>',data: getCitiesByProvinceName(_province),value: _city,onChange: function(a, b) {
           var d = getAreasByCityCode(b);
           c.setValue(""), c.setData(d)
       }}), c = new LocalSelector({el: $("#area"),defaultItem: '<option value="" data-code="">区/县</option>',data: getAreasByCityName(_city),value: _area});
   new YS.Form({form: $("#my-form"),beforeSubmit: function(a) {
           var b = [];
           return a.province || b.push("请选择省/直辖市"), a.city || b.push("请选择城市"), a.area || b.push("请选择区/县"), console.log(a), b.length ? (utils.showAlert({content: b.join("<br />")}), !1) : !0
       },successHandler: function(a, b) {
           1 === a.resultCode ? utils.showAlert({content: a.resultMessage,onButtonClick: function() {
                   utils.goUrl(a.goUrl || b)
               }}) : utils.showAlert({content: a.resultMessage})
       }})
});
