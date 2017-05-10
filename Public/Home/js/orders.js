$(function () {
//	var url="http://"+location.host+"/";
	var url="http://"+location.host+"/wanbaobao/";
//	订单操作	
//	取消订单
	$(".cancer-order").on("click",function () {
		var _this = $(this).parents(".order-container");
		$.ajax({
			url:url+"index.php/Home/User/deleteOrder",
			type:"post",
			dataType:"json",
			data:{"ordersn":_this.attr("order_sn")},
			success: function (e) {
				_this.remove();
			}
		})
	})
	
//  确认订单
	$(".affirm-order").on("click",function () {
		var _this = $(this).parents(".order-container");
		$.ajax({
			url:url+"index.php/Home/User/confirmOrder",
			type:"post",
			dataType:"json",
			data:{"ordersn":_this.attr("order_sn")},
			success: function (e) {
				_this.remove();
			}
		})
	})
	
//	退换货
	$(".back-order").on("click",function () {
		alert("请联系客服");
	})
})
