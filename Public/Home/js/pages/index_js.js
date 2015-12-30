/*
* @Author: anchen
* @Date:   2015-11-09 11:47:47
* @Last Modified by:   anchen
* @Last Modified time: 2015-11-10 14:22:12
*/

'use strict';

$(function(){
    var bannerb=640/350;
    var tbh=$('.top-banner1').width();
    $('.top-banner1').height(tbh/bannerb);
    var bannerb1=600/230;
    var tbh2=$('.top-banner2').width();
    $('.top-banner2').height(tbh2/bannerb1);
    $('.hdnr2 a span').height($('.hdnr2 a span').width());

    var a = $("#loading"), b = $("#no-item"), c = new CIScrollLoad({wrap: $(window),content: $(document),lead: 100,loadApi: _listApi,loadType: "post",loadData: {page: ++_page,cateId: window._cateId},beforeLoad: function() {
        a.css("display", "")
    },afterLoad: function(d) {
        d.error ? utils.showAlert({title: "提示",content: d.message}) : (1 !== _page || d.value || b.css("display", ""), a.fadeOut(), _listMoreHandler(d.value), d.value.isNextPage ? c.loadData = {page: ++_page,cateId: window._cateId} : c.isEnd = !0)
    }});
    c.load()
})
