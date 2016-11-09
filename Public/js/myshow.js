$(document).ready(function() {
	/*
	 * 调节左右宽度
	 */
	$('#menu').css("width", $('#cmenu').css("width"));
	$('#box').css("width", $('#bigbox').css("width"));
	$('#menu').css("height", document.documentElement.clientHeight);
	$(window).resize(function() {
		$('#menu').css("width", $('#cmenu').css("width"));
		$('#box').css("width", $('#bigbox').css("width"));
		$('#menu').css("height", document.documentElement.clientHeight);
	});
	/*
	 * 观看/取消观看按钮
	 */
	$('.watchbtn').click(function() {
		var eid = $(this).attr('eid');
		var iswatch = $(this).attr('iswatch');
		var btn = $(this);
		$.ajax({
			url: watchurl,
			data: {
				eid: eid,
				iswatch: iswatch
			},
			type: 'post',
			success: function(data) {
				if (data == 1) {
					if (iswatch == 1) {
						btn.attr('iswatch', 0)
						.removeClass("glyphicon-ok")
						.addClass("glyphicon-eye-open")
						.html(" 已看?");
					} else {
						btn.attr('iswatch', 1)
						.removeClass("glyphicon-eye-open")
						.addClass("glyphicon-ok")
						.html(" 已看!");
					}
				} else {
					alert("标记失败");
				}
			}
		});
	});
	/*
	 * 关注/取消关注按钮
	 */
	$('.followbtn').click(function() {
		var sid = $(this).attr('sid');
		var isfollow = $(this).attr('isfollow');
		var btn = $(this);
		$.ajax({
			url: followurl,
			data: {
				sid: sid,
				isfollow: isfollow
			},
			type: 'post',
			success: function(data) {	
				if (data == 1) {					
						if (isfollow == 1) {
							btn.attr('isfollow', 0)
							.removeClass("glyphicon-remove")
							.addClass("glyphicon-plus")
							.html(" 添加关注");
						} else {
							btn.attr('isfollow', 1)
							.removeClass("glyphicon-plus")
							.addClass("glyphicon-remove")
							.html(" 取消关注");
						}				
				} else {
					alert("标记失败");
				}
			}
		});
	});
	/*
	 * 控制日历大小
	 */
	$('#cal').calendar({
		onSelected: function(view, date, data) {
			data = Date.parse(date);
			data = data / 1000;
			window.location.href = indexurl + '/star/' + data;
		}
	});
});