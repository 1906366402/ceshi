//计算器弹窗_投资
$(function()
{
	//投资计算器
	$(".z_jisuan_numx").on('input',function(){
		var sum_money=0;
		$(".z_jisuan_numx").each(function(){
			var alone_money=$(this).val();
			if(alone_money=="")alone_money=0;
			if(!$.isNumeric(alone_money))
			{
				alert("请输入数字");
				$(this).val("");
				return;
			}
			sum_money += parseInt(alone_money);
		})
		$("#z_jisuan_sumx").val(sum_money);
	});
})

//获取加盟礼包
function h_hui_tijiao()
{	
	var zje = $("#z_jisuan_sumx").val();
	var tel = $("#p_sq_telnum").val();
	if( tel== '请输入手机号码' )
	{
		$("#p_sq_telnum").focus();
		return false;
	}
	var isMobile=/^(?:13\d|15\d|17\d|14\d|18\d)\d{5}(\d{3}|\*{3})$/; //手机号码验证规则
	if(!isMobile.test(tel))
	{
		$("#p_sq_telnum").val("手机号码格式不正确");
		$("#p_sq_telnum").css("color","#f00");
		return false;
	}

}









