var showMenu = function(){
	if($('#win').length == 0) return;
	var oWin = document.getElementById("win");
	var oLay = document.getElementById("overlay");	
	var oBtn = document.getElementById("popmenu");
	var oClose = document.getElementById("close");
	oBtn.onclick = function (){
		oLay.style.display = "block";
		oWin.style.display = "block"	
	};
	oLay.onclick = function (){
		oLay.style.display = "none";
		oWin.style.display = "none"	
	}	
}

$(function(){
	$(".plug-menu").click(function(){
	var span = $(this).find("span");
	if(span.attr("class") == "open"){
			span.removeClass("open");
			span.addClass("close");
			$(".plug-btn").removeClass("open");
			$(".plug-btn").addClass("close");
	}else{
			span.removeClass("close");
			span.addClass("open");
			$(".plug-btn").removeClass("close");
			$(".plug-btn").addClass("open");
	}
	});
	$(".plug-menu").on('touchmove',function(event){event.preventDefault();});

	
	showMenu();
});


window.onload = function (){
	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
		WeixinJSBridge.call('hideToolbar');
		window.shareData = {
			"imgUrl": "",
			"timeLineLink": "",
			"sendFriendLink": "",
			"weiboLink": "",
			"tTitle": "",
			"tContent": "",
			"fTitle": "",
			"fContent": "",
			"wContent": ""
		};

		// 发送给好友
		WeixinJSBridge.on('menu:share:appmessage', function (argv) {
			WeixinJSBridge.invoke('sendAppMessage', {
				"img_url": window.shareData.imgUrl,
				"img_width": "640",
				"img_height": "640",
				"link": window.shareData.sendFriendLink,
				"desc": window.shareData.fContent,
				"title": window.shareData.fTitle
			}, function (res) {
				_report('send_msg', res.err_msg);
			})
		});

		// 分享到朋友圈
		WeixinJSBridge.on('menu:share:timeline', function (argv) {
			WeixinJSBridge.invoke('shareTimeline', {
				"img_url": window.shareData.imgUrl,
				"img_width": "640",
				"img_height": "640",
				"link": window.shareData.timeLineLink,
				"desc": window.shareData.tContent,
				"title": window.shareData.tTitle
			}, function (res) {
				_report('timeline', res.err_msg);
			});
		});

		// 分享到微博
		WeixinJSBridge.on('menu:share:weibo', function (argv) {
			WeixinJSBridge.invoke('shareWeibo', {
				"content": window.shareData.wContent,
				"url": window.shareData.weiboLink,
			}, function (res) {
				_report('weibo', res.err_msg);
			});
		});
	}, false)
};

    