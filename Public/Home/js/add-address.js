$(function (){
	(function () {
//		var url="http://"+location.host+"/";
		var url="http://"+location.host+"/wanbaobao/";
		var province_id="";
		var city_id="";
		var country_id="";
	//	选择城市
		$("#change-address").on("click",function () {
			$("#alert-bg").show();
		})
		$("#alert-bg").on("click",function () {
			$("#alert-bg").hide();
		})
		$(".alert-address").on("click","p",function () {
			return false;
		})
		$("#provinces").on("click","li", function () {
			var city = this.innerHTML;
			province_id = $(this).attr("data-region_id");  //城市 id;
			$(this).addClass("li-active").siblings().removeClass("li-active");
			$("#city").val(this.innerHTML);
			$.ajax({
				url:url+"index.php/Home/User/getRegion",
				type:"post",
				datatype: "json",
				data: {"region_id":province_id},
				success: function (e) {
					if(e.error == 0){
						var _html = "";
						for(var i=0; i<e.data.length; i++){
							_html+='<li data-region_id='+e.data[i].region_id+'>'+e.data[i].title+'</li>';
						}
						$("#alert-bg2").show();
						$("#alert-citys").html(
							'<p>请选择市/区</p>\
							<ul id="citys" >'+_html+'</ul>'
						)
						$("#citys").on("click","li", function () {
							var city2=this.innerHTML;
							$(this).addClass("li-active").siblings().removeClass("li-active");
							city_id = $(this).attr("data-region_id");
							$("#alert-bg2").hide();
							$("#city").val(city+' '+this.innerHTML); 
							$.ajax({
								url:"http://"+location.host+"/index.php/Home/User/getRegion",
								type:"post",
								datatype: "json",
								data: {"region_id":city_id},
								success: function (e) {
									if(e.error == 0){
										var _html = "";
										for(var i=0; i<e.data.length; i++){
											_html+='<li data-region_id='+e.data[i].region_id+'>'+e.data[i].title+'</li>';
										}
										$("#alert-bg3").show();
										$("#alert-countrys").html(
											'<p>请选择县/镇</p>\
											<ul id="countrys" >'+_html+'</ul>'
										)
										$("#countrys").on("click","li", function () {
											$(this).addClass("li-active").siblings().removeClass("li-active");
											country_id = $(this).attr("data-region_id");
											$("#alert-bg3").hide();
											$("#city").val(city+' '+city2+' '+this.innerHTML); 
											$("#id_content").attr({
												"province_id":province_id,
												"city_id":city_id,
												"country_id":country_id
											})
										})
									}
								}
							})
						})
						$("#alert-bg2").on("click",function () {
							$("#alert-bg2").hide();
							return false;
						})
						$("#alert-bg3").on("click",function () {
							$("#alert-bg3").hide();
							return false;
						})
					}
				}
			});
		})
		
	//	详细地址
		$("#detail-address").on("click",function () {
			this.className="active";
			if(this.innerHTML=="请填写详细地址，例如街道等"){
				this.innerHTML="";
			}
		})
		
		//电话或手机验证规则
		jQuery.validator.addMethod("tm", function (value, element) {
		    var tm=/(^1[3|4|5|7|8]\d{9}$)|(^\d{3,4}-\d{7,8}$)|(^\d{7,8}$)|(^\d{3,4}-\d{7,8}-\d{1,4}$)|(^\d{7,8}-\d{1,4}$)/;
		    return this.optional(element) || (tm.test(value));
		}, "请检查格式");
		
	//保存
		$("#save").on("click",function () {
			$("#sub").click();
			//	表单验证
			})
			$("#jsForm").validate({
				submitHandler: function() {
					//验证通过后 的js代码写在这里
				
					if($("#detail-address").val() == "请填写详细地址，例如街道等" ||$("#detail-address").val() == ""){
						$("#area").text("请填写详细地址");
					}else{
						$("#area").text("");
						$.ajax({
							url:"http://"+location.host+"/index.php/Home/User/addAddress",
							type:"post",
							dataType: "json",
							data: {
								"goods_id":$("body").attr('data-goods_id'),
								"quantity":$("body").attr("data-quantity"),
								"province_id":$("#id_content").attr("province_id"),
								"city_id":$("#id_content").attr("city_id"),
								"country_id":$("#id_content").attr("country_id"),
								"detail":$("#detail-address").val(),
								"tel":$("#delivery-phone").val(),
								"recept_name":$("#delivery-man").val(),
								"from":$("body").attr("data-from"),
								"recept_id":$("body").attr("data-recept_id")
							},
							success: function (e) {
								console.log(e)
								if(e.error == '0'){
									window.location.href=e.url;
								}
							}
						});
					}
				}
			})
		
	})()
})
