$(function () {
//	var url="http://"+location.host+"/";
	var url="http://"+location.host+"/wanbaobao/";
	
	var flag=0;
//	判断是否登录
	$.ajax({
		url: url+"index.php/Home/User/m",
		type: "post",
		dataType:"json",
		success: function (e) {
			if(e.error=="0"){
				flag=1;
				$("#login").text(e.data.tel);
				$("#iflogin").show();
			}else if(e.error=="1") {
				$("#login").text("请点击登录");
				$("#iflogin").hide();
				flag=0;
			}
		}
	})
	
//	点击登录
	$(".login").on("click",function () {
		if(flag==0){
			window.location.href="login.html";
		}
	})
	
//	弹框
	$("#contact-service").click(function () {
		$("#alert-bg").show();
	})
	$("#cancel").click(function () {
		$("#alert-bg").hide();
	})
	$("#affirm").click(function () {
		$("#alert-bg").hide();
	})
	
//	退出登录
	$("#iflogin").on("click",function () {
		$.ajax({
			url:url+"index.php/Home/User/outUser",
			type: "post",
			dataType:"json",
			success: function (e) {
				if(e.error==0){
					window.location.href="../Shop/index.html";
				}
			}
		})
	})
})
