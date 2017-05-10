$(function () {
//	var url="http://"+location.host+"/";
	var url="http://"+location.host+"/wanbaobao/";
	go();
	
	//首页
	$("#img-box").children("img").on("click",function () {
		if($(this).index()==0){
			window.location.href=url+"index.php/Home/Shop/imgDetail.html";
		}
	})
})
