/*!
 * M.YS.COM v0.0.0
 * Copyright 2015
 * Author burning <iburning@live.cn>
 */
$(function(){var a=$("#loading"),b=$("#no-item"),c=new CIScrollLoad({wrap:$(window),content:$(document),lead:100,loadApi:_listApi,loadType:"post",loadData:{page:_page++},beforeLoad:function(){a.css("display","")},afterLoad:function(d){d.error?utils.showAlert({title:"提示",content:d.message}):(1!==_page||d.value||b.css("display",""),a.fadeOut(),_listMoreHandler(d.value),d.value.isNextPage?c.loadData={page:_page++}:c.isEnd=!0)}});c.load()});