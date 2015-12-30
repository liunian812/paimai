//<![CDATA[
$(function(){
	(function(){
		var m=$("#js .aa").size();
		$("#jsNav .trigger").eq(0).css("background","url(images/yuan2.png) no-repeat")
		var curr = 0;
		$("#jsNav .trigger").each(function(i){
			$(this).click(function(){
								   if($(this).hasClass("xz")){
					
					}
					else{
					curr = i;
					$("#js .aa").children("img").fadeOut("slow");
					$("#js .aa").eq(i).children("img").fadeIn("slow");
					$(this).css("background","url(images/yuan2.png) no-repeat")
					$(this).siblings("a").css("background","url(images/yuan1.png) no-repeat")
					$("#jsNav .trigger").removeClass("xz");
					$("#jsNav .trigger").eq(i).addClass("xz")
					}
				return false;
			});
		});
		
		var pg = function(flag){
			//flag:true表示前翻， false表示后翻
			if (flag) {
				if (curr == 0) {
					todo = m-1;
				} else {
					todo = (curr - 1) % m;
				}
			} else {
				todo = (curr + 1) % m;
			}
			$("#jsNav .trigger").eq(todo).click();
		};
		
		//前翻
		$("#prev").click(function(){
			pg(true);
			return false;
		});
		
		//后翻
		$("#next").click(function(){
			pg(false);
			return false;
		});
		
		//自动翻
		var timer = setInterval(function(){
			todo = (curr + 1) % m;
			$("#jsNav .trigger").eq(todo).click();
		},4000);
		
		//鼠标悬停在触发器上时停止自动翻
		$("#jsNav a").hover(function(){
				clearInterval(timer);
			},
			function(){
				timer = setInterval(function(){
					todo = (curr + 1) % m;
					$("#jsNav .trigger").eq(todo).click();
				},4000);			
			}
		);
		$("#js .imgb").hover(function(){
				clearInterval(timer);
			},
			function(){
				timer = setInterval(function(){
					todo = (curr + 1) % m;
					$("#jsNav .trigger").eq(todo).click();
				},3500);			
			}
		);
	})();
});

//]]>

//懒人图库 www.lanrentuku.com

