$(function () {
	var url="http://"+location.host+"/";
//	var url="http://"+location.host+"/wanbaobao/";
	$.ajax({
		url:url+"index.php/Home/Buy/Shop/list",
		type:"get",
		dataType:"json",
		success: function (e) {
			console.log(e)
		}
	})
})