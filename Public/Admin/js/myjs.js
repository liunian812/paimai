// JavaScript Document
$(function(){
    var winH = $(window).height();
    var successH = $('.success').height();
    $('.success').css('margin-top',(winH - successH)/2+'px');

    var errorH = $('.error').height();
    $('.error').css('margin-top',(winH - errorH)/2+'px');

    var tableH = $('table').height();
    $('table').css('margin-top',(winH-tableH)/2+'px')
})
