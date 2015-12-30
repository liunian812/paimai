/*!
 * M.YS.COM v0.0.0
 * Copyright 2015
 * Author burning <iburning@live.cn>
 */
$(function(){var a=$("#my-form");if(a.find(".btn-get-code").length){new ValidateCodeButton({button:a.find(".btn-get-code"),mobile:a.find('input[name="mobile"]')})}new YS.Form({form:a,successHandler:function(a,b){1===a.resultCode?utils.showAlert({content:a.resultMessage,onButtonClick:function(){utils.goUrl(a.goUrl||b)}}):utils.showAlert({content:a.resultMessage})}})});