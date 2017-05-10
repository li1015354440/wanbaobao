//font-size换算
onresize = function() {
	document.documentElement.style.fontSize = innerWidth / 3.75 + 'px';
}
document.documentElement.style.fontSize = innerWidth / 3.75 + 'px';

//轮播图
function go () {
	
	$dragBln = false;
	
	$(".main_img").touchSlider({
		flexible : true,
		speed : 300,
		btn_prev : $("#btn_prev"),
		btn_next : $("#btn_next"),
		paging : $("#pagination span"),
		counter : function (e){
			$("#pagination span").removeClass("active").eq(e.current-1).addClass("active");//图片顺序点切换
		}
	});
	
	$(".main_img").bind("mousedown", function() {
		$dragBln = false;
	});
	
	$(".main_img").bind("dragstart", function() {
		$dragBln = true;
	});
	
	$(".main_img a").click(function(){
		if($dragBln) {
			return false;
		}
	});
	
	timer = setInterval(function(){
		$("#btn_next").click();
	}, 5000);
	
	$("#banner-box").hover(function(){
		clearInterval(timer);
	},function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		},5000);
	});
	
	$(".main_img").bind("touchstart",function(){
		clearInterval(timer);
	}).bind("touchend", function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		}, 5000);
	});
	
};