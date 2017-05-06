$(function () {
//	订单操作	
//	取消订单
	$(".cancer-order").on("click",function () {
		var _this = $(this).parents(".order-container");
		$.ajax({
			url:"http://"+location.host+"/wanbaobao/index.php/Home/User/deleteOrder",
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
			url:"http://"+location.host+"/wanbaobao/index.php/Home/User/confirmOrder",
			type:"post",
			dataType:"json",
			data:{"ordersn":_this.attr("order_sn")},
			success: function (e) {
				_this.remove();
			}
		})
	})
})
