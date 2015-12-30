function log(a) {
    if (window._isDebug) {
        var a = $log.html();
        a += "<br />" + a + "<br />", $log.css("display", ""), $log.html(a)
    }
}

function downloadImage(a, serverId) {
    utils.ajax({
        url: _getImageUrlApi,
        type: "post",
        dataType: "json",
        data: {
            mediaId: serverId
        },
        success: function(res) {
            a.find("input").val(res.value.info.url)
        },
        err: function(res) {
            utils.showAlert({
                content: '上传失败-' + res
            })
        }
    })
}

$(function() {
    $(".uploadImage").on("click", function() {
        var a = $(this);
        // 选择图片
        wx.chooseImage({
            count: 1, // 默认9
            sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
            success: function(res) {
                var localIds = res.localIds;
                if (localIds.length) {
                    var vimg = a.find("img");
                    if (vimg) {
                        vimg.remove();
                    }
                    var view = new Image;
                    view.src = localIds[0];
                    a.append($(view));

                    wx.uploadImage({
                        localId: localIds[0], // 需要上传的图片的本地ID，由chooseImage接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function(res) {
                            var serverId = res.serverId; // 返回图片的服务器端ID
                            downloadImage(a, serverId);
                        },
                        cancel: function(res) {
                            utils.showAlert({
                                content: '上传失败-' + res
                            })
                        }
                    });
                } else {
                    utils.showAlert({
                        content: '至少选择图片'
                    })
                }
            },
            cancel: function(res) {
                utils.showAlert({
                    content: '选择图片失败-' + res
                })
            }
        })
    });

    new YS.Form({
        form: $("#my-form"),
        beforeSubmit: function(a) {
            var b = [];
            return b.length ? (utils.showAlert({
                content: b.join("<br />")
            }), !1) : !0
        },
        successHandler: function(a, b) {
            1 === a.resultCode ? utils.goUrl(a.goUrl || b) : utils.showAlert({
                content: a.resultMessage,
                onButtonClick: function() {
                    if (a.goUrl != '') {
                        utils.goUrl(a.goUrl)
                    }
                }
            })
        }
    });
});
