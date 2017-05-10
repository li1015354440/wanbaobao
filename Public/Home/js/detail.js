$(function () {
//	var url="http://"+location.host+"/";
	var url="http://"+location.host+"/wanbaobao/";
//	轮播图
	go();
	(function () {
		var flag=0;
		var count=0;
		//	添加购物车
		$("#add-car").on("click",function () {
			$("#alert-bg").show();
			$("#animate-bg").animate({
				"bottom":"0"
			},200)
			count = parseInt($("#count").text());
		})
		
//		立即购买
		$("#buy-now").on("click",function () {
			$("#alert-bg").show();
			$("#animate-bg").animate({
				"bottom":"0"
			},200)
			count = parseInt($("#count").text());
			flag=1;
		})
		
//		增加数量
		$("#count-more").on("click",function () {
			count++;
			$("#count").text(count);
			return false;
		})
			
//		减少数量
		$("#count-shot").on("click",function () {
			if(count==1){
				count=1;
			}else {
				count--;
			}
			$("#count").text(count);
			return false;
		})
		$("#alert-bg").on("click","li:not(:last-child)",function () {
			return false;
		})
		$("#alert-bg").on("click",function (e) {
			$("#animate-bg").animate({
				"bottom":"-3rem"
			},200,function () {
				$("#alert-bg").hide();
			})
		})
		
	//	确认
		$("#add-sure").on("click",function () {
			if(flag==0){
				$.ajax({
					url:url+"index.php/Home/Buy/cartadd",
					type:"post",
					datatype: "json",
					data: {"goods_id":$("body").attr("data-goods_id"),"quantity":count},
					success: function (e) {
						$("#count").text(1);
						if(e.error == 1){
							window.location.href=url+"/index.php/Home/User/login.html";
						}
					}
				});
			}else{
				$.ajax({
					url: url+"index.php/Home/User/m",
					type: "post",
					dataType:"json",
					success: function (e) {
						if(e.error=="0"){
							window.location.href=url+"/index.php/Home/Buy/pay/goods_id/"+$("body").attr("data-goods_id")+"/quantity/"+count+"/from/buy_now";
						}else if(e.error=="1") {
							window.location.href=url+"/index.php/Home/User/login.html";
						}
					}
				})
			}
		})
	})()
})
