$(document).ready(function() {
	getRate();
	$("#rating button").click(
		function() {
			var rate = $(this).val();
			var eid = $('#episode').attr('eid');
			$.ajax({
				url: runrating,
				data: {
					eid: eid,
					rate: rate
				},
				type: 'post',
				success: function(data) {
					if (data >= 1 && data <= 5) {
						$("#rating button").each(function() {
							if ($(this).val() == data) {
								$(this).removeClass("btn-default")
								.addClass("btn-primary");
							} else {
								$(this).removeClass("btn-primary")
								.addClass("btn-default");
							}
						});
					} else {
						alert(data);
					}

				}
			});
		}

	);

	/*
	 * 评论框控制
	 */
	$("#comment").keyup(function() {
		var maxlength = 140;
		var content = $(this).val();
		var length = trimStr(content).length;
		//最大允许输入140个字
		if (length == 0) {
			$('#submitbtn').attr("disabled", "disabled");
				$("#send_info").html("还可输入");
				$("#send_num").html('140');
		} else if (length > maxlength) {
			$("#send_info").html("已经超出");
			$("#send_num").html(length - maxlength);
			$("#send_num").css("color", "red");
			$('#submitbtn').attr("disabled", "disabled");
		} else {
			$("#send_info").html("还可输入");
			$("#send_num").html(maxlength - length);
			$("#send_num").css("color", "black");
			$('#submitbtn').removeAttr("disabled");
		}
	});
});
/*
 * 去除字符串前后的空格
 */
function trimStr(str) {
	return str.replace(/(^\s*)|(\s*$)/g, "");
}
/*
 * 加载用户评分
 */
function getRate() {
	var eid = $('#episode').attr('eid');
	$.ajax({
		url: getrate,
		data: {
			eid: eid
		},
		type: 'post',
		success: function(data) {
			$("#rating button").each(function() {
				if ($(this).val() == data) {
					$(this).removeClass("btn-default");
					$(this).addClass("btn-primary");
				}
			});
		}
	});
}

function runRating() {
	var eid = $('#episode').attr('eid');
	$.ajax({
		url: getrate,
		data: {
			eid: eid
		},
		type: 'post',
		success: function(data) {
			alert(data);
			$("#rating button").each(function() {
				if ($(this).val() == data) {
					$(this).removeClass("btn-default");
					$(this).addClass("btn-primary");
				}
			});
		}
	});

}