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
function initImages(a) {
   a.length >= maxCount && $filePicker.css("display", "none");
   for (var b = 0; b < a.length; b++) {
       var c = new FileInput;
       c.onRemove = function() {
           changeCount(-1)
       }, c.setImageSrc(a[b]), c.setFileValue(a[b]), c.showOptions(), $fileList.append(c.el), changeCount(1)
   }
}
function afterChooseImage(a) {
   log("afterChooseImage: " + JSON.stringify(localIds));
   var b = maxCount - count;
   if (localIds = a, localIds.length > b)
       return void utils.showAlert({content: "当前只能上传" + b + "张图片"});
   if (localIds.length) {
       utils.showLoading({title: "上传中 1/" + localIds.length});
       var c = new FileInput;
       c.onRemove = function() {
           changeCount(-1)
       }, c.init(), setTimeout(function() {
           addPreviewImage(c, localIds[i]), uploadImage(c, localIds[i])
       }, 100)
   }
}
function addPreviewImage(a, b) {
   changeCount(1), a.setImageSrc(b), $fileList.append(a.el)
}
function uploadImage(a, b) {
   wx.uploadImage({localId: b,isShowProgressTips: 0,success: function(b) {
           afterUploadImage(a, b.serverId)
       },error: function(a) {
           log(JSON.stringify(a))
       }})
}
function downloadImage(a, b) {
   log("downloadImage: " + b), utils.ajax({url: _getImageUrlApi,type: "post",dataType: "json",data: {mediaId: b},success: function(b) {
           log("downloadImage: " + JSON.stringify(b)), log(typeof b.resultCode + " " + b.resultCode), 1 === b.resultCode && (currentDownload++, utils.showLoading({title: "上传中 " + currentDownload + "/" + localIds.length}), currentDownload >= localIds.length && (currentDownload = 0, i = 0, utils.hideLoading()), a.uploadSuccess(b.value.info.url), log("url" + b.value.info.url))
       },err: function(a) {
           log(JSON.stringify(a))
       }})
}
function afterUploadImage(a, b) {
   if (downloadImage(a, b), i++, i < localIds.length) {
       var a = new FileInput;
       a.onRemove = function() {
           changeCount(-1)
       }, a.init(), setTimeout(function() {
           addPreviewImage(a, localIds[i]), uploadImage(a, localIds[i])
       }, 100)
   }
}
function changeCount(a) {
   count += a, count >= maxCount ? $filePicker.css("display", "none") : $filePicker.css("display", "")
}
wx.ready(function() {
   log("wx ready")
}), wx.error(function(a) {
   log(JSON.stringify(a))
});
var $filePicker = null, $fileList = null, $uploadTip = null, $log = null, maxCount = _fileNumLimit, count = 0, i = 0, currentDownload = 0, localIds = [];
$(function() {
   $filePicker = $("#file-picker"), $fileList = $("#file-list"), $uploadTip = $("#upload-tip"), $log = $("#log"), $filePicker.on("click", function(a) {
       wx.chooseImage({success: function(a) {
               afterChooseImage(a.localIds)
           },cancel: function(a) {
               log(JSON.stringify(a))
           }})
   }), initImages(_images);
   new YS.Form({form: $("#my-form"),beforeSubmit: function(a) {
           var b = [];
           return b.length ? (utils.showAlert({content: b.join("<br />")}), !1) : !0
       },successHandler: function(a, b) {
           1 === a.resultCode ? utils.goUrl(a.goUrl || b) : utils.showAlert({content: a.resultMessage,
               onButtonClick: function() {
                   if (a.goUrl != '') {
                       utils.goUrl(a.goUrl)
                   }
               }
            })
       }});
   $(".btn-cancel").on("click", function(a) {
       utils.goUrl(_goodsListUrl)
   })
});
