//font-size换算
onresize = function() {
	document.documentElement.style.fontSize = innerWidth / 3.75 + 'px';
}
document.documentElement.style.fontSize = innerWidth / 3.75 + 'px';

//轮播图
function go() {
	var timer = null;
	var n = 0;
	timer = setInterval(function() {
		if(n >= 3) {
			n = 0;
		} else {
			n++;
		}
		$("#img-box").animate({
			"left": -($("#img-box").children("img").eq(0).width()) + "px"
		}, 500, function() {
			$("#img-box").css("left", 0);
			$("#img-box").children("img").eq(0).appendTo("#img-box");
			$("#pagination").children("span").eq(n).addClass("active").siblings().removeClass("active");
		});
	}, 3000)
};